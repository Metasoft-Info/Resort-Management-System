<section class="mb-5 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:mb-6 sm:p-6">
    <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-indigo-600">Billing choice</p>
    <h2 class="mb-4 text-lg font-bold text-gray-900 sm:text-xl">How should the selected rooms be billed?</h2>
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
        <label class="flex min-w-0 cursor-pointer items-start gap-3 rounded-xl border-2 border-gray-200 p-4 transition hover:border-indigo-300">
            <input type="radio" name="billing_mode" value="separate" checked onchange="renderGroupRoomGuests()" class="mt-1 shrink-0">
            <span class="min-w-0">
                <strong class="block text-gray-900">One combined bill</strong>
                <span class="mt-1 block text-sm leading-relaxed text-gray-600">All selected rooms stay together under the main customer and use one booking bill.</span>
            </span>
        </label>
        <label class="flex min-w-0 cursor-pointer items-start gap-3 rounded-xl border-2 border-gray-200 p-4 transition hover:border-indigo-300">
            <input type="radio" name="billing_mode" value="group" onchange="renderGroupRoomGuests()" class="mt-1 shrink-0">
            <span class="min-w-0">
                <strong class="block text-gray-900">Separate bill for each room</strong>
                <span class="mt-1 block text-sm leading-relaxed text-gray-600">Rooms are booked together, but each gets its own booking and bill. You can use the main customer’s name or enter another guest per room.</span>
            </span>
        </label>
    </div>
</section>

<script>
const groupRoomGuests = {};
const groupBookingRequestId = crypto.randomUUID();

function appendGroupGuestInput(container, guest, roomId, key, labelText, type = 'text', required = false) {
    const label = document.createElement('label');
    label.className = 'block min-w-0 text-sm font-medium text-gray-700';
    label.textContent = labelText;

    const input = type === 'textarea' ? document.createElement('textarea') : document.createElement('input');
    if (type !== 'textarea') input.type = type;
    input.id = `group_guest_${key}_${roomId}`;
    input.className = 'mt-1 block w-full min-w-0 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100';
    input.value = guest[key] ?? '';
    input.autocomplete = 'off';
    if (required) input.required = true;
    if (type === 'number') {
        input.min = '1';
        input.max = '100';
    }
    if (key === 'customer_name') input.placeholder = 'Enter the name to print on this room bill';
    input.addEventListener('input', () => { guest[key] = input.value; });

    label.appendChild(input);
    container.appendChild(label);
}

function renderGroupRoomGuests() {
    const panel = document.getElementById('groupRoomGuests');
    const target = document.getElementById('groupRoomGuestFields');
    if (!panel || !target) return;

    const enabled = document.querySelector('input[name="billing_mode"]:checked')?.value === 'group';
    panel.classList.toggle('hidden', !enabled);
    target.replaceChildren();
    if (!enabled || !Array.isArray(selectedRooms)) return;

    selectedRooms.forEach(room => {
        const roomId = String(room.roomId);
        const guest = groupRoomGuests[roomId] ||= {use_main_customer: true, number_of_guests: 1};
        const card = document.createElement('article');
        card.className = 'min-w-0 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5';

        const heading = document.createElement('div');
        heading.className = 'mb-4 flex min-w-0 flex-wrap items-start justify-between gap-2 border-b border-gray-100 pb-3';
        const roomTitle = document.createElement('div');
        roomTitle.className = 'min-w-0';
        const title = document.createElement('h3');
        title.className = 'break-words font-bold text-gray-900';
        title.textContent = `Room ${room.roomNumber}`;
        const rent = document.createElement('p');
        rent.className = 'mt-1 text-sm text-gray-500';
        const roomNights = Number(room.nights) || 1;
        const roomRent = (Number(room.pricePerNight) || 0) * roomNights;
        rent.textContent = `${roomNights} night${roomNights === 1 ? '' : 's'} · BDT ${roomRent.toLocaleString()}`;
        roomTitle.append(title, rent);
        const billBadge = document.createElement('span');
        billBadge.className = 'shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700';
        billBadge.textContent = 'Separate room bill';
        heading.append(roomTitle, billBadge);
        card.appendChild(heading);

        const modeLabel = document.createElement('label');
        modeLabel.className = 'block text-sm font-semibold text-gray-700';
        modeLabel.textContent = 'Bill name';
        const mode = document.createElement('select');
        mode.id = `group_guest_mode_${roomId}`;
        mode.className = 'mt-1 block w-full min-w-0 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100';
        mode.add(new Option('Use the main customer’s name', 'main'));
        mode.add(new Option('Enter a different guest', 'different'));
        mode.value = guest.use_main_customer === false ? 'different' : 'main';
        mode.addEventListener('change', () => {
            guest.use_main_customer = mode.value === 'main';
            renderGroupRoomGuests();
        });
        modeLabel.appendChild(mode);
        card.appendChild(modeLabel);

        const mainCustomerHint = document.createElement('p');
        mainCustomerHint.className = 'mt-2 text-xs leading-relaxed text-gray-500';
        mainCustomerHint.dataset.mainGuestName = 'true';
        const mainName = document.getElementById('customer_name')?.value.trim();
        mainCustomerHint.textContent = `Main customer: ${mainName || 'enter the name in Customer Information above'}`;
        card.appendChild(mainCustomerHint);

        if (guest.use_main_customer === false) {
            const guestPanel = document.createElement('div');
            guestPanel.className = 'mt-4 rounded-xl border border-indigo-100 bg-indigo-50/60 p-3 sm:p-4';
            const guestTitle = document.createElement('p');
            guestTitle.className = 'mb-3 text-sm font-semibold text-indigo-900';
            guestTitle.textContent = `Guest details for Room ${room.roomNumber}`;
            guestPanel.appendChild(guestTitle);

            const primaryFields = document.createElement('div');
            primaryFields.className = 'grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2';
            appendGroupGuestInput(primaryFields, guest, roomId, 'customer_name', 'Bill guest name *', 'text', true);
            appendGroupGuestInput(primaryFields, guest, roomId, 'customer_phone', 'Phone (optional)', 'tel');
            guestPanel.appendChild(primaryFields);

            const moreDetails = document.createElement('details');
            moreDetails.className = 'mt-3 rounded-lg bg-white px-3 py-2';
            const summary = document.createElement('summary');
            summary.className = 'cursor-pointer text-sm font-medium text-indigo-700';
            summary.textContent = 'Add more guest details (optional)';
            moreDetails.appendChild(summary);
            const extraFields = document.createElement('div');
            extraFields.className = 'mt-3 grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2';
            appendGroupGuestInput(extraFields, guest, roomId, 'customer_nid', 'NID');
            appendGroupGuestInput(extraFields, guest, roomId, 'company_name', 'Company');
            appendGroupGuestInput(extraFields, guest, roomId, 'customer_email', 'Email', 'email');
            appendGroupGuestInput(extraFields, guest, roomId, 'customer_address', 'Address', 'textarea');
            moreDetails.appendChild(extraFields);
            guestPanel.appendChild(moreDetails);
            card.appendChild(guestPanel);
        }

        const guestCount = document.createElement('div');
        guestCount.className = 'mt-4 max-w-xs';
        appendGroupGuestInput(guestCount, guest, roomId, 'number_of_guests', 'Guests in this room', 'number', true);
        card.appendChild(guestCount);
        target.appendChild(card);
    });
}

document.addEventListener('input', event => {
    if (event.target?.id !== 'customer_name') return;
    const name = event.target.value.trim() || 'enter the name in Customer Information above';
    document.querySelectorAll('[data-main-guest-name]').forEach(node => {
        node.textContent = `Main customer: ${name}`;
    });
});
</script>
