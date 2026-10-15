const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const source = fs.readFileSync(path.resolve(__dirname, '../../resources/views/admin/premium-booking/guest-history-script.blade.php'), 'utf8')
    .replace(/^<script>\s*/, '').replace(/<\/script>\s*$/, '')
    .replace(/const guestHistoryConfig = \{[\s\S]*?\};/, `const guestHistoryConfig = {
        endpoint: '/admin/premium-booking/search-customer', today: '2026-10-15', tomorrow: '2026-10-16', preselectedProfile: null
    };`);

function setup(fetchImpl) {
    const elements = new Map();
    function element(id) {
        if (!elements.has(id)) elements.set(id, {
            value: '', disabled: false, innerHTML: '', textContent: '', listeners: {}, requests: 0,
            classList: {add() {}, remove() {}},
            addEventListener(type, handler) { this.listeners[type] = handler; },
            replaceChildren() { this.innerHTML = ''; this.textContent = ''; },
            scrollIntoView() { this.scrolled = true; },
            requestSubmit() { this.requests++; },
        });
        return elements.get(id);
    }
    const context = vm.createContext({
        document: {getElementById: element, querySelector: () => element('directOption')},
        window: {location: {search: ''}},
        selectedRooms: [], currentSearchDates: {},
        updateSelectedRoomsPanel() {},
        fillCustomerInfo(profile) { for (const [key, value] of Object.entries(profile)) element(key).value = value; },
        fetch: fetchImpl, AbortController, URLSearchParams, Intl, Date, setTimeout: () => 1, clearTimeout() {},
    });
    vm.runInContext(source, context);
    return {context, element};
}

function result(name = 'Returning Guest') {
    const profile = {customer_name: name, customer_phone: '01711111111', company_name: 'Example Company'};
    return {success: true, customer: profile, total_bookings: 1, profile_url: '/admin/customers/01711111111', history: [{
        id: 50, profile, rooms: ['301', '204'], check_in_date: '2026-10-11', check_out_date: '2026-10-15', nights: 4,
        status: 'checked_out', bill_total: 12000, paid: 11000, due: 1000, view_url: '/admin/bookings/50',
    }]};
}
const response = data => ({ok: true, json: async () => data});

test('history escapes guest text and reuses contact details while keeping selected dates and rooms', async () => {
    const {context, element} = setup(async () => response(result('<img src=x onerror=alert(1)>')));
    context.selectedRooms = [{roomId: 14}];
    context.currentSearchDates = {checkIn: '2026-10-20', checkOut: '2026-10-22'};
    element('checkInDate').value = '2026-10-20';
    element('checkOutDate').value = '2026-10-22';
    await context.searchCustomer('01711111111');
    assert.match(element('customerSearchResults').innerHTML, /&lt;img/);
    assert.doesNotMatch(element('customerSearchResults').innerHTML, /<img src=x/);
    context.useGuestHistory(0);
    assert.equal(element('customer_name').value, '<img src=x onerror=alert(1)>');
    assert.equal(element('checkInDate').value, '2026-10-20');
    assert.equal(context.selectedRooms[0].roomId, 14);
    assert.equal(element('advance_payment').value, '');
    assert.equal(element('discount_amount').value, '');
});

test('direct check-in resets future dates, clears room selection, and searches current availability', async () => {
    const {context, element} = setup(async () => response(result()));
    element('checkInDate').value = '2026-10-20';
    element('checkOutDate').value = '2026-10-22';
    context.selectedRooms = [{roomId: 14}];
    context.currentSearchDates = {checkIn: '2026-10-20', checkOut: '2026-10-22'};
    await context.searchCustomer('01711111111');
    context.useGuestHistory(0, true);
    assert.equal(element('checkInDate').value, '2026-10-15');
    assert.equal(element('checkOutDate').value, '2026-10-16');
    assert.equal(element('status').value, 'checked_in');
    assert.equal(element('directOption').disabled, false);
    assert.equal(context.selectedRooms.length, 0);
    assert.equal(element('searchRoomsForm').requests, 1);
});

test('an earlier slow lookup cannot overwrite a newer phone match', async () => {
    const pending = [];
    const {context, element} = setup(() => new Promise(resolve => pending.push(resolve)));
    const first = context.searchCustomer('01711111111');
    const second = context.searchCustomer('01722222222');
    pending[1](response(result('New match')));
    await second;
    pending[0](response(result('Old match')));
    await first;
    assert.equal(element('customer_name').value, 'New match');
    assert.match(element('customerSearchResults').innerHTML, /New match/);
    assert.doesNotMatch(element('customerSearchResults').innerHTML, /Old match/);
    assert.equal(element('guestSearchButton').disabled, false);
});
