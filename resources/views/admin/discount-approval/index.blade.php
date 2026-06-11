@extends('layouts.admin')

@section('title', 'Approval Discount')
@section('header', 'Approval Discount')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-6 shadow-xl text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Approval Discount</h1>
                    <p class="text-amber-100 text-sm">Review and approve staff discount requests</p>
                </div>
            </div>
        </div>
    </div>

    @if($needsMigration ?? false)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 text-center">
        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2"></i>
        <h3 class="text-red-800 font-bold text-lg">Database Migration Required</h3>
        <p class="text-red-600 text-sm mt-1">The discount approval columns are missing. Please run the following command on your server:</p>
        <code class="block bg-red-100 text-red-800 rounded-lg px-4 py-2 mt-3 font-mono text-sm">php artisan migrate</code>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-amber-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pending</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Approved</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $approvedCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-times-circle text-red-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Rejected</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $rejectedCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-orange-500"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pending Amount</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($pendingAmount, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Type Toggle + Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 space-y-4">
        <!-- Type Toggle Tabs -->
        <div class="flex gap-2">
            <a href="{{ route('admin.discount-approval.index', array_merge(request()->except('type'), ['type' => 'all'])) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $typeFilter === 'all' ? 'bg-amber-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-list mr-1"></i>All
            </a>
            <a href="{{ route('admin.discount-approval.index', array_merge(request()->except('type'), ['type' => 'room'])) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $typeFilter === 'room' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-bed mr-1"></i>Room Bookings
            </a>
            <a href="{{ route('admin.discount-approval.index', array_merge(request()->except('type'), ['type' => 'convention'])) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $typeFilter === 'convention' ? 'bg-violet-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                <i class="fas fa-building-columns mr-1"></i>Convention Bookings
            </a>
        </div>
        <form method="GET" class="flex flex-col md:flex-row gap-3">
            @if($typeFilter !== 'all')
            <input type="hidden" name="type" value="{{ $typeFilter }}">
            @endif
            <div class="flex gap-3 flex-1">
                <div class="flex-1">
                    <label class="text-xs text-gray-500 font-semibold uppercase mb-1 block">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                </div>
                <div class="flex-1">
                    <label class="text-xs text-gray-500 font-semibold uppercase mb-1 block">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                </div>
            </div>
            <div class="md:w-48">
                <label class="text-xs text-gray-500 font-semibold uppercase mb-1 block">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-amber-600 text-white px-5 py-2 rounded-xl hover:bg-amber-700 transition font-semibold shadow-md">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.discount-approval.index', ['type' => $typeFilter]) }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl hover:bg-gray-200 transition flex items-center">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px]">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Guest / Customer</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Requested By</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allBookings as $booking)
                    @php
                        $isRoom = $booking instanceof \App\Models\Booking;
                        $type = $isRoom ? 'Room' : 'Convention';
                        $customerName = $booking->customer_name ?? 'N/A';
                        $customerPhone = $booking->customer_phone ?? '';
                        $discountAmount = $isRoom
                            ? (($booking->discount_type === 'percentage' && $booking->discount_percentage > 0)
                                ? ($booking->getCalculatedTotal() * $booking->discount_percentage / 100)
                                : ($booking->discount_amount ?? 0))
                            : ($booking->discount ?? 0);
                        $discountType = $isRoom
                            ? ($booking->discount_type === 'percentage' ? $booking->discount_percentage . '%' : ($booking->discount_type === 'flat' ? 'Flat' : '-'))
                            : ($booking->discount_type ?? 'Flat');
                        $reference = $isRoom ? ($booking->discount_reference ?? '-') : '-';
                        $status = $booking->discount_status ?? 'pending';
                        $routeType = $isRoom ? 'room' : 'convention';
                        $id = $booking->id;
                    @endphp
                    <tr class="hover:bg-amber-50/20 transition">
                        <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $booking->created_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $isRoom ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-violet-50 text-violet-700 border border-violet-100' }}">{{ $type }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-semibold text-gray-800 text-sm">{{ $customerName }}</div>
                            <div class="text-xs text-gray-400">{{ $customerPhone }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-7 h-7 bg-gradient-to-br from-gray-300 to-gray-400 rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white text-[10px] font-bold">{{ strtoupper(substr($booking->discountRequestedBy->name ?? ($booking->createdBy->name ?? 'S'), 0, 1)) }}</span>
                                </div>
                                <span class="text-sm text-gray-600">{{ $booking->discountRequestedBy->name ?? ($booking->createdBy->name ?? 'Staff') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="font-bold text-amber-600 text-sm">{{ number_format($discountAmount, 0) }}</div>
                            <div class="text-xs text-gray-400">{{ $discountType }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $reference }}</td>
                        <td class="px-4 py-4 text-center">
                            @if($status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5 animate-pulse"></span>Pending
                            </span>
                            @elseif($status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>Approved
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Rejected
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-gray-500">
                            @if($booking->discountApprovedBy)
                            <div class="flex items-center justify-center">
                                <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full flex items-center justify-center mr-1.5">
                                    <span class="text-white text-[9px] font-bold">{{ strtoupper(substr($booking->discountApprovedBy->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-xs">{{ $booking->discountApprovedBy->name }}</span>
                            </div>
                            <div class="text-[10px] text-gray-400">{{ $booking->discount_approved_at ? \Carbon\Carbon::parse($booking->discount_approved_at)->format('d-m-Y H:i') : '' }}</div>
                            @else
                            <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="showDetails('{{ $routeType }}', {{ $id }})" class="w-8 h-8 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition flex items-center justify-center" title="Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                @if($status === 'pending')
                                <form action="{{ route('admin.discount-approval.approve', [$routeType, $id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition flex items-center justify-center" title="Approve">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.discount-approval.reject', [$routeType, $id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 transition flex items-center justify-center" title="Reject">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check-double text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No discount requests found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999] overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 my-8 max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <h3 class="text-white font-bold text-lg"><i class="fas fa-file-invoice mr-2"></i>Booking Details</h3>
            <button onclick="closeDetailsModal()" class="text-white/80 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div id="detailsContent" class="p-6">
            <div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-amber-500"></i><p class="text-gray-600 mt-2">Loading...</p></div>
        </div>
    </div>
</div>

<script>
function showDetails(type, id) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('detailsContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-amber-500"></i><p class="text-gray-600 mt-2">Loading...</p></div>';
    fetch('/admin/discount-approval/' + type + '/' + id, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(data => {
        content.innerHTML = renderDetails(data);
    }).catch(() => {
        content.innerHTML = '<div class="text-center py-8 text-red-500">Failed to load details</div>';
    });
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function renderDetails(data) {
    const b = data.booking;
    const isRoom = data.type === 'room';
    let html = '<div class="space-y-5">';

    // Header
    html += '<div class="bg-gradient-to-r from-slate-50 to-gray-50 rounded-xl p-4 border border-gray-100">';
    html += '<div class="flex items-center justify-between">';
    html += '<div><span class="text-xs text-gray-400 font-semibold uppercase">Booking ID</span><p class="text-xl font-bold text-gray-800">#' + b.id + '</p></div>';
    html += '<div class="text-right"><span class="px-3 py-1 rounded-full text-xs font-bold ' + (b.discount_status === 'approved' ? 'bg-emerald-100 text-emerald-700' : b.discount_status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') + '">' + (b.discount_status ? b.discount_status.charAt(0).toUpperCase() + b.discount_status.slice(1) : 'Pending') + '</span></div>';
    html += '</div></div>';

    // Customer Info
    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    html += '<div class="bg-white rounded-xl border border-gray-100 p-4">';
    html += '<h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Customer Information</h4>';
    html += '<div class="space-y-2">';
    html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Name</span><span class="text-sm font-semibold text-gray-800">' + (b.customer_name || '-') + '</span></div>';
    html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Phone</span><span class="text-sm font-semibold text-gray-800">' + (b.customer_phone || '-') + '</span></div>';
    html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Email</span><span class="text-sm font-semibold text-gray-800">' + (b.customer_email || '-') + '</span></div>';
    html += '<div class="flex justify-between"><span class="text-sm text-gray-500">NID</span><span class="text-sm font-semibold text-gray-800">' + (b.customer_nid || '-') + '</span></div>';
    html += '</div></div>';

    // Booking Info
    html += '<div class="bg-white rounded-xl border border-gray-100 p-4">';
    html += '<h4 class="text-xs font-bold text-gray-400 uppercase mb-3">' + (isRoom ? 'Room' : 'Convention') + ' Information</h4>';
    html += '<div class="space-y-2">';
    if (isRoom) {
        const rooms = b.booking_rooms && b.booking_rooms.length > 0 ? b.booking_rooms.map(br => br.room ? br.room.room_number : 'N/A').join(', ') : (b.room ? b.room.room_number : 'N/A');
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Room(s)</span><span class="text-sm font-semibold text-gray-800">' + rooms + '</span></div>';
        const ciDate = b.check_in_date ? (b.check_in_date.length > 10 ? b.check_in_date.substring(0, 10) : b.check_in_date) : '-';
        const coDate = b.check_out_date ? (b.check_out_date.length > 10 ? b.check_out_date.substring(0, 10) : b.check_out_date) : '-';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Check-in</span><span class="text-sm font-semibold text-gray-800">' + ciDate + ' ' + (b.check_in_time || '') + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Check-out</span><span class="text-sm font-semibold text-gray-800">' + coDate + ' ' + (b.check_out_time || '') + '</span></div>';
    } else {
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Hall</span><span class="text-sm font-semibold text-gray-800">' + (b.convention_hall ? b.convention_hall.name : 'N/A') + '</span></div>';
        const evDate = b.event_date ? (b.event_date.length > 10 ? b.event_date.substring(0, 10) : b.event_date) : '-';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Event Date</span><span class="text-sm font-semibold text-gray-800">' + evDate + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Time Slot</span><span class="text-sm font-semibold text-gray-800">' + (b.time_slot || '-') + '</span></div>';
    }
    html += '</div></div></div>';

    // Financial Summary
    html += '<div class="bg-white rounded-xl border border-gray-100 p-4">';
    html += '<h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Financial Summary</h4>';
    html += '<div class="space-y-2">';
    if (isRoom) {
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Total Amount</span><span class="text-sm font-semibold text-gray-800">' + (b.total_amount || 0).toLocaleString() + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Advance Payment</span><span class="text-sm font-semibold text-gray-800">' + (b.advance_payment || 0).toLocaleString() + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Remaining</span><span class="text-sm font-semibold text-gray-800">' + (b.remaining_payment || 0).toLocaleString() + '</span></div>';
        // Extra charges
        if (b.extra_charges && b.extra_charges > 0) {
            html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Extra Charges</span><span class="text-sm font-semibold text-orange-600">+' + (b.extra_charges).toLocaleString() + '</span></div>';
        }
    } else {
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Hall Rent</span><span class="text-sm font-semibold text-gray-800">' + (b.hall_rent || 0).toLocaleString() + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Food Cost</span><span class="text-sm font-semibold text-gray-800">' + (b.food_cost || 0).toLocaleString() + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Total Amount</span><span class="text-sm font-semibold text-gray-800">' + (b.total_amount || 0).toLocaleString() + '</span></div>';
        if (b.extra_charges && b.extra_charges > 0) {
            html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Extra Charges</span><span class="text-sm font-semibold text-orange-600">+' + (b.extra_charges).toLocaleString() + '</span></div>';
        }
    }
    html += '<div class="flex justify-between pt-2 border-t border-gray-100"><span class="text-sm font-bold text-amber-600">Discount</span><span class="text-sm font-bold text-amber-600">' + data.discountAmount.toLocaleString() + ' (' + (b.discount_type || 'Flat') + ')</span></div>';
    if (b.discount_reference) {
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Discount Reference</span><span class="text-sm font-semibold text-gray-800">' + b.discount_reference + '</span></div>';
    }
    html += '</div></div>';

    // Discount Approval Info
    html += '<div class="bg-white rounded-xl border border-gray-100 p-4">';
    html += '<h4 class="text-xs font-bold text-gray-400 uppercase mb-3">Discount Approval</h4>';
    html += '<div class="space-y-2">';
    const statusLabel = b.discount_status ? b.discount_status.charAt(0).toUpperCase() + b.discount_status.slice(1) : 'Pending';
    html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Status</span><span class="text-sm font-semibold text-gray-800">' + statusLabel + '</span></div>';
    html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Requested By</span><span class="text-sm font-semibold text-gray-800">' + (data.discountRequestedByName || 'System') + '</span></div>';
    if (b.discount_approved_by) {
        const appDate = b.discount_approved_at ? (b.discount_approved_at.length > 16 ? b.discount_approved_at.substring(0, 16).replace('T', ' ') : b.discount_approved_at) : '-';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Approved By</span><span class="text-sm font-semibold text-gray-800">' + (data.discountApprovedByName || '-') + '</span></div>';
        html += '<div class="flex justify-between"><span class="text-sm text-gray-500">Approved At</span><span class="text-sm font-semibold text-gray-800">' + appDate + '</span></div>';
    }
    html += '</div></div>';

    html += '</div>';
    return html;
}

// Close modal on backdrop click
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailsModal();
});
// Close on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDetailsModal();
});
</script>
@endsection
