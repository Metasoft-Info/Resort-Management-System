<section class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-6 mb-6">
    <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 mb-1">Start here</p>
    <h2 class="text-xl font-bold text-gray-900 mb-4">How would you like to book?</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <label class="flex items-start gap-3 border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-indigo-400">
            <input type="radio" name="billing_mode" value="separate" checked onchange="renderGroupRoomGuests()" class="mt-1">
            <span><strong class="block text-gray-900">Separate Bill</strong><span class="block text-sm text-gray-600 mt-1">Your usual booking flow. Selected rooms stay together on one guest booking and invoice.</span></span>
        </label>
        <label class="flex items-start gap-3 border-2 border-indigo-200 bg-indigo-50 rounded-xl p-4 cursor-pointer hover:border-indigo-500">
            <input type="radio" name="billing_mode" value="group" onchange="renderGroupRoomGuests()" class="mt-1">
            <span><strong class="block text-indigo-900">Group Bill <span class="text-xs font-normal">/ room-wise bills</span></strong><span class="block text-sm text-indigo-700 mt-1">Book together, bill separately. One booking and invoice per room, with optional individual guest details.</span></span>
        </label>
    </div>
</section>
<script>
const groupRoomGuests = {};
const groupBookingRequestId = crypto.randomUUID();
function renderGroupRoomGuests() {
    const panel = document.getElementById('groupRoomGuests');
    const target = document.getElementById('groupRoomGuestFields');
    if (!panel || !target) return;
    const enabled = document.querySelector('input[name="billing_mode"]:checked')?.value === 'group';
    panel.classList.toggle('hidden', !enabled);
    target.replaceChildren();
    if (!enabled) return;
    selectedRooms.forEach(room => {
        const guest = groupRoomGuests[room.roomId] ||= {number_of_guests: 1};
        const card = document.createElement('div');
        card.className = 'rounded-lg border border-gray-200 p-4 bg-gray-50';
        const title = document.createElement('h3');
        title.className = 'font-bold text-indigo-800 mb-3';
        title.textContent = `Room ${room.roomNumber} · BDT ${(room.pricePerNight * room.nights).toLocaleString()} room rent`;
        card.appendChild(title);
        const fields = document.createElement('div');
        fields.className = 'grid grid-cols-1 sm:grid-cols-2 gap-3';
        [['customer_name','Guest name','text'], ['customer_phone','Phone','tel'], ['customer_nid','NID','text'],
         ['company_name','Company','text'], ['customer_email','Email','email'], ['customer_address','Address','text'],
         ['number_of_guests','Guests in this room','number']].forEach(([key, labelText, type]) => {
            const label = document.createElement('label');
            label.className = 'block text-sm text-gray-700';
            label.appendChild(document.createTextNode(labelText));
            const input = document.createElement('input');
            input.type = type;
            input.className = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm';
            input.value = guest[key] ?? '';
            input.placeholder = type === 'number' ? '1' : 'Use main customer';
            if (type === 'number') { input.min = '1'; input.max = '100'; input.required = true; }
            input.addEventListener('input', () => { guest[key] = input.value; });
            label.appendChild(input); fields.appendChild(label);
        });
        card.appendChild(fields); target.appendChild(card);
    });
}
</script>
