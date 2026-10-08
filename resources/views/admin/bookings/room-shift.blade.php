@if($booking->roomShifts->isNotEmpty())
<section class="bg-white border border-indigo-100 rounded-xl shadow-sm p-5 my-6">
    <h2 class="text-lg font-bold text-gray-900 mb-1">Room shift history</h2>
    <p class="text-sm text-gray-500 mb-4">Previous rooms, stay dates and rent remain part of this booking's bill.</p>
    <div class="overflow-x-auto"><table class="w-full text-sm text-left">
        <thead class="bg-indigo-50"><tr><th class="p-3">Room change</th><th class="p-3">Previous stay</th><th class="p-3">Previous rent</th><th class="p-3">Recorded by</th><th class="p-3">Reason</th></tr></thead>
        <tbody>@foreach($booking->roomShifts as $shift)
        <tr class="border-b border-gray-100">
            <td class="p-3 font-semibold">{{ $shift->from_room_number }} → {{ $shift->to_room_number }}</td>
            <td class="p-3">{{ $shift->occupied_from->format('d M Y') }} – {{ $shift->shift_date->format('d M Y') }}<br><span class="text-gray-500">{{ $shift->billed_nights }} night(s)</span></td>
            <td class="p-3">BDT {{ number_format($shift->billed_amount, 2) }}<br><span class="text-gray-500">{{ number_format($shift->previous_rate, 2) }}/night</span></td>
            <td class="p-3">{{ $shift->shiftedBy?->name ?? '—' }}<br><span class="text-gray-500">{{ $shift->created_at->format('d M Y, h:i A') }}</span></td>
            <td class="p-3">{{ $shift->reason }}</td>
        </tr>@endforeach</tbody>
    </table></div>
</section>
@endif
<div id="roomShiftModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4 print:hidden" role="dialog" aria-modal="true" aria-labelledby="roomShiftTitle">
    <form id="roomShiftForm" class="bg-white rounded-2xl shadow-xl w-full max-w-xl p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between gap-4 mb-4"><h2 id="roomShiftTitle" class="text-xl font-bold">Shift guest to another room</h2><button type="button" onclick="closeRoomShift()" aria-label="Close">✕</button></div>
        <p class="text-sm text-gray-600 mb-4">Shift today, {{ today('Asia/Dhaka')->format('d M Y') }}, until {{ $booking->check_out_date->format('d M Y') }}. Previous nights keep their original charge; the new room is charged from today.</p>
        <label class="block font-semibold text-sm mb-2" for="shiftFromRoom">Current room</label>
        <select id="shiftFromRoom" name="from_room_id" class="w-full border rounded-lg p-3 mb-4" required>
            @foreach($booking->getAllRooms() as $shiftRoom)<option value="{{ $shiftRoom->id }}">Room {{ $shiftRoom->room_number }}</option>@endforeach
        </select>
        <label class="block font-semibold text-sm mb-2" for="shiftToRoom">Available replacement room</label>
        <select id="shiftToRoom" name="to_room_id" class="w-full border rounded-lg p-3 mb-4" required><option value="">Loading available rooms…</option></select>
        <label class="block font-semibold text-sm mb-2" for="shiftReason">Reason for shifting</label>
        <textarea id="shiftReason" name="reason" class="w-full border rounded-lg p-3" rows="2" required maxlength="1000" placeholder="Guest request, room issue, upgrade…"></textarea>
        <p id="roomShiftError" class="text-sm text-red-600 mt-3" role="alert"></p>
        <div class="flex justify-end gap-3 mt-4"><button type="button" onclick="closeRoomShift()" class="px-4 py-2 border rounded-lg">Cancel</button><button id="roomShiftSubmit" type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg disabled:opacity-50">Confirm room shift</button></div>
    </form>
</div>
<script>
function closeRoomShift() { document.getElementById('roomShiftModal').classList.add('hidden'); document.getElementById('roomShiftModal').classList.remove('flex'); }
async function openRoomShift() {
    const modal = document.getElementById('roomShiftModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    const select = document.getElementById('shiftToRoom');
    const button = document.getElementById('roomShiftSubmit');
    const error = document.getElementById('roomShiftError');
    button.disabled = true; error.textContent = '';
    select.replaceChildren(new Option('Loading available rooms…', ''));
    try {
        const response = await fetch(@json(route('admin.bookings.shift-rooms', $booking)), {headers: {'Accept': 'application/json'}});
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'Unable to check availability.');
        select.replaceChildren(new Option(data.rooms.length ? 'Choose a room' : 'No rooms available for the remaining stay', ''));
        data.rooms.forEach(room => select.add(new Option(`Room ${room.room_number} · ${room.room_type?.name || ''} · BDT ${Number(room.price_per_night ?? room.room_type?.base_price ?? 0).toLocaleString()}/night`, room.id)));
        button.disabled = data.rooms.length === 0;
    } catch (e) { error.textContent = e.message; }
}
document.getElementById('roomShiftForm').addEventListener('submit', async event => {
    event.preventDefault();
    const button = document.getElementById('roomShiftSubmit');
    if (button.disabled) return;
    button.disabled = true;
    try {
        const response = await fetch(@json(route('admin.bookings.shift-room', $booking)), {
            method: 'POST', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
            body: new FormData(event.target)
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || Object.values(data.errors || {}).flat()[0] || 'Room shift failed.');
        location.reload();
    } catch (e) { document.getElementById('roomShiftError').textContent = e.message; button.disabled = false; }
});
</script>
