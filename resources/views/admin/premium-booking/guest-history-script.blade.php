<script>
const guestHistoryConfig = {
    endpoint: @json(route('admin.premium-booking.search-customer')),
    today: @json(today('Asia/Dhaka')->toDateString()),
    tomorrow: @json(today('Asia/Dhaka')->addDay()->toDateString()),
    preselectedProfile: @json($returningGuestProfile ?? null),
};
let guestHistoryRecords = [];
let guestHistoryRequest = null;
let guestHistorySequence = 0;

function escapeGuestHistory(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[character]));
}

function guestHistoryDate(value) {
    if (!value) return '-';
    return new Intl.DateTimeFormat('en-GB', {day: '2-digit', month: 'short', year: 'numeric', timeZone: 'Asia/Dhaka'}).format(new Date(value + 'T12:00:00+06:00'));
}

function guestHistoryMoney(value) {
    return 'BDT ' + Number(value || 0).toLocaleString('en-BD', {maximumFractionDigits: 2});
}

async function searchCustomer(value = null, options = {}) {
    const query = String(value ?? document.getElementById('searchPhone').value).trim();
    const results = document.getElementById('customerSearchResults');
    const button = document.getElementById('guestSearchButton');
    const sequence = ++guestHistorySequence;
    guestHistoryRequest?.abort();
    guestHistoryRecords = [];
    if (!query) {
        results.textContent = 'Enter a phone, NID or passport number to find previous stays.';
        button.disabled = false;
        return;
    }

    document.getElementById('searchPhone').value = query;
    guestHistoryRequest = new AbortController();
    button.disabled = true;
    results.innerHTML = '<p class="py-3 text-sm text-indigo-700"><i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>Finding recent stays...</p>';
    try {
        const response = await fetch(`${guestHistoryConfig.endpoint}?query=${encodeURIComponent(query)}`, {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            signal: guestHistoryRequest.signal,
            cache: 'no-store',
        });
        if (!response.ok) throw new Error('Unable to load guest history. Please try again.');
        const data = await response.json();
        if (sequence !== guestHistorySequence) return;
        if (!data.success || !data.customer) {
            results.innerHTML = '<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600"><i class="fas fa-user-plus mr-2" aria-hidden="true"></i>No previous stays found. Enter the new guest details below.</div>';
            return;
        }

        guestHistoryRecords = data.history || [];
        if (options.autofill !== false) {
            fillCustomerInfo(data.customer);
            showSelectedGuestNotice(data.customer.customer_name);
        }
        renderGuestHistory(data, options.autofill !== false);
        if (options.scroll) document.getElementById('returningGuestSearch').scrollIntoView({behavior: 'smooth', block: 'start'});
    } catch (error) {
        if (error.name !== 'AbortError' && sequence === guestHistorySequence) {
            results.textContent = error.message;
            results.classList.add('text-red-600');
        }
    } finally {
        if (sequence === guestHistorySequence) button.disabled = false;
    }
}

function showSelectedGuestNotice(name, direct = false) {
    const notice = document.getElementById('selectedGuestNotice');
    notice.textContent = direct
        ? `${name}: ready for check-in. Select an available room and confirm the new bill below.`
        : `${name}: guest details are ready. Choose new dates and an available room to continue.`;
    notice.classList.remove('hidden');
}

function renderGuestHistory(data, expanded) {
    const statuses = {
        checked_in: ['Checked in', 'bg-green-100 text-green-800'],
        checked_out: ['Checked out', 'bg-gray-100 text-gray-700'],
        confirmed: ['Confirmed', 'bg-blue-100 text-blue-800'],
        pending: ['Pending', 'bg-yellow-100 text-yellow-800'],
        cancelled: ['Cancelled', 'bg-red-100 text-red-800'],
    };
    const cards = guestHistoryRecords.map((stay, index) => {
        const status = statuses[stay.status] || ['Unknown', 'bg-gray-100 text-gray-700'];
        return `<article class="min-w-0 rounded-xl border border-gray-200 bg-white p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Booking #${String(stay.id).padStart(5, '0')}</span>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold ${status[1]}">${status[0]}</span>
            </div>
            <h3 class="mt-3 break-words font-bold text-gray-900">${escapeGuestHistory(stay.profile.customer_name)}</h3>
            <p class="mt-1 break-words text-xs text-gray-500">${escapeGuestHistory(stay.profile.company_name || stay.profile.customer_phone)}</p>
            <p class="mt-3 text-sm font-semibold text-indigo-700"><i class="fas fa-bed mr-2" aria-hidden="true"></i>Room ${escapeGuestHistory(stay.rooms.join(', ') || '-')}</p>
            <p class="mt-1 text-xs leading-5 text-gray-600">${escapeGuestHistory(guestHistoryDate(stay.check_in_date))} &rarr; ${escapeGuestHistory(guestHistoryDate(stay.check_out_date))}<span class="ml-2 text-gray-400">${Number(stay.nights)} night(s)</span></p>
            <dl class="mt-3 grid grid-cols-3 gap-2 rounded-lg bg-gray-50 p-3 text-xs">
                <div><dt class="text-gray-500">Bill</dt><dd class="mt-1 break-words font-semibold text-gray-900">${guestHistoryMoney(stay.bill_total)}</dd></div>
                <div><dt class="text-gray-500">Paid</dt><dd class="mt-1 break-words font-semibold text-green-700">${guestHistoryMoney(stay.paid)}</dd></div>
                <div><dt class="text-gray-500">Due</dt><dd class="mt-1 break-words font-semibold ${Number(stay.due) > 0 ? 'text-red-600' : 'text-gray-700'}">${guestHistoryMoney(stay.due)}</dd></div>
            </dl>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button type="button" onclick="useGuestHistory(${index})" class="rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Use details</button>
                <button type="button" onclick="useGuestHistory(${index}, true)" class="rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-800 hover:bg-green-100">Check in today</button>
                <a href="${escapeGuestHistory(stay.view_url)}" target="_blank" rel="noopener" class="ml-auto text-xs font-medium text-gray-500 underline hover:text-indigo-700">View booking</a>
            </div>
        </article>`;
    }).join('');

    const results = document.getElementById('customerSearchResults');
    results.classList.remove('text-red-600');
    results.innerHTML = `<div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0"><p class="break-words font-bold text-gray-900">${escapeGuestHistory(data.customer.customer_name)}</p>
                <p class="mt-1 text-sm text-gray-600">${escapeGuestHistory(data.customer.customer_phone)} <span class="mx-1">&middot;</span> ${Number(data.total_bookings)} previous booking(s)</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="useGuestHistory(0)" class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-indigo-700">Use latest details</button>
                <a href="${escapeGuestHistory(data.profile_url)}" target="_blank" rel="noopener" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700">Full history <i class="fas fa-arrow-up-right-from-square ml-1 text-xs" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    <details class="mt-3 rounded-xl border border-gray-200 bg-gray-50" ${expanded ? 'open' : ''}>
        <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-gray-800">Recent stays (${guestHistoryRecords.length}) <span class="ml-1 font-normal text-gray-500">Choose the guest details to reuse</span></summary>
        <div class="grid gap-3 border-t border-gray-200 p-3 md:grid-cols-2 xl:grid-cols-3">${cards}</div>
    </details>`;
}

function useGuestHistory(index, directCheckin = false) {
    const stay = guestHistoryRecords[index];
    if (!stay) return;
    fillCustomerInfo(stay.profile);
    if (directCheckin) {
        const checkIn = document.getElementById('checkInDate');
        const checkOut = document.getElementById('checkOutDate');
        if (checkIn.value !== guestHistoryConfig.today || !checkOut.value || checkOut.value <= guestHistoryConfig.today) {
            checkIn.value = guestHistoryConfig.today;
            checkOut.value = guestHistoryConfig.tomorrow;
        }
        updateDirectCheckinOption();
        document.getElementById('status').value = 'checked_in';
        if (!selectedRooms.length || currentSearchDates.checkIn !== checkIn.value || currentSearchDates.checkOut !== checkOut.value) {
            selectedRooms = [];
            updateSelectedRoomsPanel();
            document.getElementById('searchRoomsForm').requestSubmit();
        }
    }
    showSelectedGuestNotice(stay.profile.customer_name, directCheckin);
    const target = selectedRooms.length ? document.getElementById('bookingForm') : document.getElementById('searchRoomsForm');
    target.scrollIntoView({behavior: 'smooth', block: 'start'});
}

document.getElementById('searchPhone').addEventListener('keydown', event => {
    if (event.key === 'Enter') { event.preventDefault(); searchCustomer(); }
});
let guestPhoneTimer;
document.getElementById('customer_phone').addEventListener('input', event => {
    clearTimeout(guestPhoneTimer);
    const value = event.target.value.trim();
    document.getElementById('selectedGuestNotice').classList.add('hidden');
    guestHistoryRequest?.abort();
    ++guestHistorySequence;
    document.getElementById('guestSearchButton').disabled = false;
    document.getElementById('customerSearchResults').replaceChildren();
    guestHistoryRecords = [];
    if (value.replace(/\D/g, '').length >= 7) {
        guestPhoneTimer = setTimeout(() => searchCustomer(value, {autofill: false}), 500);
    }
});

function updateDirectCheckinOption() {
    const checkIn = document.getElementById('checkInDate').value;
    const checkOut = document.getElementById('checkOutDate').value;
    const option = document.querySelector('#status option[value="checked_in"]');
    option.disabled = !checkIn || !checkOut || checkIn > guestHistoryConfig.today || checkOut <= guestHistoryConfig.today;
    if (option.disabled && document.getElementById('status').value === 'checked_in') document.getElementById('status').value = 'confirmed';
}
document.getElementById('checkInDate').addEventListener('change', updateDirectCheckinOption);
document.getElementById('checkOutDate').addEventListener('change', updateDirectCheckinOption);
document.getElementById('status').addEventListener('change', updateDirectCheckinOption);
updateDirectCheckinOption();

if (guestHistoryConfig.preselectedProfile) {
    fillCustomerInfo(guestHistoryConfig.preselectedProfile);
    showSelectedGuestNotice(guestHistoryConfig.preselectedProfile.customer_name);
    searchCustomer(guestHistoryConfig.preselectedProfile.customer_phone, {autofill: false});
} else {
    const repeatPhone = new URLSearchParams(window.location.search).get('phone');
    if (repeatPhone) searchCustomer(repeatPhone);
}
</script>
