@extends('layouts.admin')
@section('content')
<div class="p-6">
    <div class="mb-6 print:hidden">
        <h1 class="text-3xl font-bold text-gray-800">রুম বুকিং রিপোর্ট</h1>
        <p class="text-gray-600 mt-2">তারিখ: {{ date('d-m-Y') }}</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.room-bookings') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">শুরুর তারিখ</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">শেষ তারিখ</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">স্ট্যাটাস</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>নিশ্চিত</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>চেক-ইন</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>চেক-আউট</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট স্ট্যাটাস</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>পেন্ডিং</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>আংশিক</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">খুঁজুন</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম / ফোন / রুম" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>ফিল্টার
                    </button>
                    <a href="{{ route('admin.reports.room-bookings') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Print Header - Invoice Style -->
    <div class="hidden print:block mb-6">
        <div class="text-center border-b-2 border-gray-700 pb-4 mb-4">
            @if($resortInfo && $resortInfo->header_logo)
                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-16 mx-auto mb-2">
            @else
                <h1 class="text-2xl font-bold text-gray-800">{{ $resortInfo->resort_name ?? 'তুফান কনভেনশন রিসোর্ট' }}</h1>
            @endif
            @if($resortInfo && $resortInfo->address)
                <p class="text-gray-600 text-sm">{{ $resortInfo->address }}</p>
            @endif
            <p class="text-gray-500 text-xs mt-1">
                @if($resortInfo)
                    @if($resortInfo->phone)Phone: {{ $resortInfo->phone }}@endif
                    @if($resortInfo->phone && $resortInfo->email) | @endif
                    @if($resortInfo->email)Email: {{ $resortInfo->email }}@endif
                @endif
            </p>
        </div>
        
        <!-- Report Title -->
        <div class="text-center mb-4">
            <h2 class="text-xl font-bold text-gray-800 tracking-wider">রুম বুকিং রিপোর্ট</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if(request('start_date') || request('end_date'))
                    তারিখ: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d-m-Y') : 'শুরু' }} 
                    থেকে {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d-m-Y') : 'শেষ' }}
                @else
                    তারিখ: {{ date('d-m-Y') }}
                @endif
                @if(request('status'))
                    | স্ট্যাটাস: {{ request('status') }}
                @endif
            </p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6 print:grid-cols-5 print:gap-2 print:text-xs">
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">মোট বুকিং</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">{{ $totalBookings }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">মোট বিল</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($totalRevenue, 0) }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">বিল জমা</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($totalAdvance, 0) }}</p>
        </div>
        <div class="bg-primary-50 rounded-lg p-4 text-center border border-primary-200 print:p-2">
            <p class="text-gray-600 text-xs">অতিরিক্ত চার্জ</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($bookings->sum('extra_charges'), 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg p-4 text-center border border-red-200 print:p-2">
            <p class="text-gray-600 text-xs">বাকি</p>
            <p class="text-xl font-bold text-red-600 print:text-base">৳{{ number_format($totalRemaining, 0) }}</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-2 mb-4 print:hidden">
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
            <i class="fas fa-print mr-2"></i>প্রিন্ট
        </button>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden print:shadow-none print:rounded-none">
        <div class="w-full">
            <table class="w-full text-sm border-collapse border border-gray-400 table-fixed">
                <thead>
                    <tr class="bg-gray-200 print:bg-gray-300">
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[7%]">তারিখ</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[9%]">মোবাইল নম্বর</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[10%]">নাম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[8%]">পেশা/কোম্পানী</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[5%]">রুম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right w-[7%]">বিল</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right w-[7%]">বিল জমা</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right w-[6%]">অতিরিক্ত</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right w-[7%]">বাকি</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[7%]">চেক ইন</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[7%]">চেক আউট</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-center w-[4%]">রাত্রি</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 w-[10%]">মন্তব্য</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 print:hidden w-[6%]">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalNights = 0; @endphp
                    @forelse($bookings as $booking)
                    @php 
                        $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
                        $totalNights += $nights;
                        $calculatedTotal = $booking->getCalculatedTotal();
                        $calculatedRemaining = $booking->getCalculatedRemaining();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->created_at)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-medium">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->company_name ?? '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-semibold text-primary-700">{{ $booking->bookingRooms->count() > 0 ? $booking->bookingRooms->map(fn($br) => $br->room->room_number)->join(', ') : ($booking->room ? $booking->room->room_number : 'N/A') }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right font-semibold">{{ number_format($calculatedTotal, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-primary-600">{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right">{{ number_format($booking->extra_charges ?? 0, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-red-600 font-semibold">{{ number_format($calculatedRemaining, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center">{{ $nights }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-xs">{{ $booking->notes ?? '' }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center print:hidden">
                            <button onclick="showGuestInfo({{ $booking->id }})" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="14" class="border border-gray-400 px-4 py-8 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="5" class="border border-gray-400 px-2 py-2 text-right">মোট:</td>
                        <td class="border border-gray-400 px-2 py-2 text-right">{{ number_format($totalRevenue, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-primary-700">{{ number_format($totalAdvance, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right">{{ number_format($bookings->sum('extra_charges'), 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-red-600">{{ number_format($totalRemaining, 0) }}</td>
                        <td colspan="2" class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 text-center">{{ $totalNights }}</td>
                        <td class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6 print:hidden">{{ $bookings->links() }}</div>

    <!-- Print Footer -->
    <div class="hidden print:block mt-6 pt-3 border-t border-gray-400 text-xs text-gray-600">
        <div class="flex justify-between">
            <div>প্রিন্ট তারিখ: {{ now()->format('d-m-Y H:i') }}</div>
            <div>Developed by Mir Javed Jeetu | 01811480222</div>
        </div>
    </div>
</div>

<!-- Guest Info Modal -->
<div id="guestInfoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl my-8 mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
            <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-file-invoice mr-2 text-primary-600"></i>বুকিং বিবরণী / ইনভয়েস</h3>
            <div class="flex gap-2">
                <button onclick="printInvoice()" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                    <i class="fas fa-print mr-1"></i>ইনভয়েস প্রিন্ট
                </button>
                <button onclick="closeGuestInfoModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div id="guestInfoContent" class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i>
                <p class="text-gray-600 mt-2">লোড হচ্ছে...</p>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Print Area for Guest Info -->
<div id="guestPrintArea" class="hidden print:block"></div>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 8mm;
    }
    body {
        font-size: 9px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    nav, header, aside, footer, .lg\:ml-64 > header, .lg\:ml-64 > footer {
        display: none !important;
    }
    .p-6 {
        padding: 0 !important;
    }
    table {
        font-size: 8px !important;
    }
    th, td {
        padding: 2px 4px !important;
    }
}

@media print {
    body.print-guest-info * {
        visibility: hidden;
    }
    body.print-guest-info #guestPrintArea,
    body.print-guest-info #guestPrintArea * {
        visibility: visible;
    }
    body.print-guest-info #guestPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 15mm;
    }
    body.print-guest-info .invoice-print {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12pt;
    }
    body.print-guest-info .invoice-print table {
        font-size: 10pt !important;
    }
    body.print-guest-info .invoice-print th,
    body.print-guest-info .invoice-print td {
        padding: 6px 10px !important;
    }
    @page {
        size: A4 portrait;
        margin: 10mm;
    }
}
</style>

<script>
// Store booking data for quick access
const bookingsData = @json($bookings->keyBy('id'));

// Store current booking ID for printing
let currentBookingId = null;

// Helper function to format date
function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

// Helper function to format currency
function formatTaka(amount) {
    return '৳' + parseFloat(amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Helper function to get status badge
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">পেন্ডিং</span>',
        'confirmed': '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">নিশ্চিত</span>',
        'checked_in': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">চেক-ইন</span>',
        'checked_out': '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">চেক-আউট</span>',
        'cancelled': '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">বাতিল</span>',
        'paid': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">পরিশোধিত</span>',
        'partial': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">আংশিক</span>'
    };
    return badges[status] || `<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">${status}</span>`;
}

function showGuestInfo(bookingId) {
    const modal = document.getElementById('guestInfoModal');
    const content = document.getElementById('guestInfoContent');
    
    // Store current booking ID for printing
    currentBookingId = bookingId;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch booking details
    fetch(`/admin/bookings/${bookingId}?ajax=1`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.booking) {
            const b = data.booking;
            
            // Calculate nights
            const checkIn = new Date(b.check_in_date);
            const checkOut = new Date(b.check_out_date);
            const nights = Math.max(1, Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)));
            
            // Build rooms list with prices
            let roomsHtml = '';
            let roomDetailsHtml = '';
            let baseAmount = 0;
            
            if (b.booking_rooms && b.booking_rooms.length > 0) {
                roomsHtml = b.booking_rooms.map(br => br.room?.room_number || 'N/A').join(', ');
                roomDetailsHtml = b.booking_rooms.map(br => {
                    const roomPrice = parseFloat(br.price_per_night) || 0;
                    const roomTotal = roomPrice * nights;
                    baseAmount += roomTotal;
                    const roomType = br.room?.room_type?.name || '';
                    return `
                        <tr class="border-b">
                            <td class="py-2 px-2">${br.room?.room_number || 'N/A'} ${roomType ? '('+roomType+')' : ''}</td>
                            <td class="py-2 px-2 text-right">${formatTaka(roomPrice)}</td>
                            <td class="py-2 px-2 text-center">${nights}</td>
                            <td class="py-2 px-2 text-right font-semibold">${formatTaka(roomTotal)}</td>
                        </tr>
                    `;
                }).join('');
            } else if (b.room) {
                roomsHtml = b.room.room_number;
                const roomPrice = parseFloat(b.room?.room_type?.base_price || b.room?.price_per_night || 0);
                baseAmount = roomPrice * nights;
                roomDetailsHtml = `
                    <tr class="border-b">
                        <td class="py-2 px-2">${b.room.room_number} ${b.room.room_type?.name ? '('+b.room.room_type.name+')' : ''}</td>
                        <td class="py-2 px-2 text-right">${formatTaka(roomPrice)}</td>
                        <td class="py-2 px-2 text-center">${nights}</td>
                        <td class="py-2 px-2 text-right font-semibold">${formatTaka(baseAmount)}</td>
                    </tr>
                `;
            }
            
            // Calculate totals
            const discountAmount = parseFloat(b.discount_amount) || 0;
            const discountPercent = parseFloat(b.discount_percentage) || 0;
            let discount = 0;
            if (b.discount_type === 'percentage' && discountPercent > 0) {
                discount = (baseAmount * discountPercent) / 100;
            } else if (b.discount_type === 'flat' && discountAmount > 0) {
                discount = discountAmount;
            }
            
            const afterDiscount = baseAmount - discount;
            const extraCharges = parseFloat(b.extra_charges) || 0;
            const vatAmount = b.vat_enabled ? (afterDiscount * 0.15) : 0;
            const grandTotal = afterDiscount + extraCharges + vatAmount;
            const advancePayment = parseFloat(b.advance_payment) || 0;
            const remaining = grandTotal - advancePayment;
            
            // Additional guests
            let additionalGuestsHtml = '';
            if (b.additional_guests && b.additional_guests.length > 0) {
                additionalGuestsHtml = `
                    <div class="mt-4 border-t pt-4">
                        <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-users mr-2"></i>অতিরিক্ত গেস্ট (${b.additional_guests.length})</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            ${b.additional_guests.map((g, i) => `
                                <div class="bg-gray-50 p-2 rounded border text-sm">
                                    <p class="font-semibold">${i+1}. ${g.name || 'N/A'}</p>
                                    <p class="text-gray-600">NID: ${g.nid || 'N/A'} | Phone: ${g.phone || 'N/A'}</p>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }
            
            // Payment history
            let paymentHistoryHtml = '';
            if (b.payments && b.payments.length > 0) {
                paymentHistoryHtml = `
                    <div class="mt-4 border-t pt-4">
                        <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-history mr-2"></i>পেমেন্ট হিস্ট্রি</h4>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-2 text-left">তারিখ</th>
                                    <th class="py-2 px-2 text-right">পরিমাণ</th>
                                    <th class="py-2 px-2 text-left">মাধ্যম</th>
                                    <th class="py-2 px-2 text-left">নোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${b.payments.map(p => `
                                    <tr class="border-b">
                                        <td class="py-2 px-2">${formatDate(p.created_at)}</td>
                                        <td class="py-2 px-2 text-right font-semibold text-green-600">${formatTaka(p.amount)}</td>
                                        <td class="py-2 px-2">${p.method || 'N/A'}</td>
                                        <td class="py-2 px-2 text-xs text-gray-600">${p.note || '-'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
            
            content.innerHTML = `
                <div class="space-y-4" id="guestInfoPrintContent">
                    <!-- Header -->
                    <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
                        <h2 class="text-xl font-bold text-gray-800">তুফান কনভেনশন রিসোর্ট</h2>
                        <p class="text-sm text-gray-600">ইনভয়েস / বুকিং বিবরণী</p>
                        <p class="text-lg font-bold text-primary-600 mt-2">Booking #${String(b.id).padStart(5, '0')}</p>
                    </div>
                    
                    <!-- Customer & Booking Info Side by Side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Info -->
                        <div class="bg-primary-50 p-4 rounded-lg border border-primary-200">
                            <h4 class="font-bold text-primary-800 mb-3 border-b pb-2"><i class="fas fa-user mr-2"></i>গ্রাহক তথ্য</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-semibold w-20 inline-block">নাম:</span> ${b.customer_name || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">ফোন:</span> ${b.customer_phone || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">NID:</span> ${b.customer_nid || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">কোম্পানি:</span> ${b.company_name || 'N/A'}</p>
                                <p><span class="font-semibold w-20 inline-block">ঠিকানা:</span> ${b.customer_address || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <!-- Booking Info -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fas fa-calendar-alt mr-2"></i>বুকিং তথ্য</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-semibold">চেক-ইন:</span> ${formatDate(b.check_in_date)} ${b.check_in_time ? 'সময়: ' + b.check_in_time : ''}</p>
                                <p><span class="font-semibold">চেক-আউট:</span> ${formatDate(b.check_out_date)} ${b.check_out_time ? 'সময়: ' + b.check_out_time : ''}</p>
                                <p><span class="font-semibold">মোট রাত্রি:</span> ${nights} রাত</p>
                                <p><span class="font-semibold">গেস্ট সংখ্যা:</span> ${b.number_of_guests || 1} জন</p>
                                <p><span class="font-semibold">স্ট্যাটাস:</span> ${getStatusBadge(b.status)}</p>
                                <p><span class="font-semibold">পেমেন্ট:</span> ${getStatusBadge(b.payment_status)}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room Details Table -->
                    <div class="mt-4">
                        <h4 class="font-bold text-gray-800 mb-2"><i class="fas fa-bed mr-2"></i>রুম বিবরণ</h4>
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-2 text-left border">রুম</th>
                                    <th class="py-2 px-2 text-right border">প্রতি রাত</th>
                                    <th class="py-2 px-2 text-center border">রাত</th>
                                    <th class="py-2 px-2 text-right border">মোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${roomDetailsHtml}
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="3" class="py-2 px-2 text-right border">সাবটোটাল:</td>
                                    <td class="py-2 px-2 text-right border">${formatTaka(baseAmount)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Payment Summary -->
                    <div class="mt-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fas fa-calculator mr-2"></i>পেমেন্ট সারাংশ</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>রুম চার্জ:</div>
                            <div class="text-right">${formatTaka(baseAmount)}</div>
                            
                            ${discount > 0 ? `
                            <div class="text-green-600">ডিসকাউন্ট ${b.discount_type === 'percentage' ? '('+discountPercent+'%)' : ''}:</div>
                            <div class="text-right text-green-600">- ${formatTaka(discount)}</div>
                            ` : ''}
                            
                            ${extraCharges > 0 ? `
                            <div>অতিরিক্ত চার্জ:</div>
                            <div class="text-right">+ ${formatTaka(extraCharges)}</div>
                            ` : ''}
                            
                            ${b.vat_enabled ? `
                            <div>VAT (15%):</div>
                            <div class="text-right">+ ${formatTaka(vatAmount)}</div>
                            ` : ''}
                            
                            <div class="font-bold text-lg border-t pt-2 mt-2">সর্বমোট:</div>
                            <div class="text-right font-bold text-lg border-t pt-2 mt-2">${formatTaka(grandTotal)}</div>
                            
                            <div class="text-green-700">অগ্রিম জমা:</div>
                            <div class="text-right text-green-700">${formatTaka(advancePayment)}</div>
                            
                            <div class="font-bold ${remaining > 0 ? 'text-red-600' : 'text-green-600'}">বাকি:</div>
                            <div class="text-right font-bold ${remaining > 0 ? 'text-red-600' : 'text-green-600'}">${formatTaka(remaining)}</div>
                        </div>
                    </div>
                    
                    ${additionalGuestsHtml}
                    
                    ${paymentHistoryHtml}
                    
                    <!-- Reference Info -->
                    ${b.reference_name || b.reference_phone ? `
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 mt-4">
                        <p class="text-sm"><i class="fas fa-phone-alt mr-2"></i><span class="font-semibold">রেফারেন্স:</span> ${b.reference_name || 'N/A'} | ফোন: ${b.reference_phone || 'N/A'}</p>
                    </div>
                    ` : ''}
                    
                    <!-- Notes -->
                    ${b.notes ? `
                    <div class="bg-gray-100 p-3 rounded-lg mt-4">
                        <p class="text-sm"><i class="fas fa-sticky-note mr-2"></i><span class="font-semibold">নোট:</span> ${b.notes}</p>
                    </div>
                    ` : ''}
                    
                    <!-- Print Footer -->
                    <div class="mt-6 pt-4 border-t-2 border-gray-300 text-center text-xs text-gray-500">
                        <p>প্রিন্ট তারিখ: ${new Date().toLocaleDateString('bn-BD')} | Tufan Convention Resort</p>
                        <p class="mt-1">Developed by Mir Javed Jeetu | 01811480222</p>
                    </div>
                </div>
            `;
        } else {
            content.innerHTML = '<div class="text-center py-8 text-red-600">তথ্য লোড করা যায়নি</div>';
        }
    })
    .catch(err => {
        console.error(err);
        content.innerHTML = '<div class="text-center py-8 text-red-600">Error loading data</div>';
    });
}

function closeGuestInfoModal() {
    const modal = document.getElementById('guestInfoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function printInvoice() {
    if (!currentBookingId) {
        alert('বুকিং তথ্য পাওয়া যায়নি');
        return;
    }
    
    // Show loading indicator
    const printBtn = document.querySelector('button[onclick="printInvoice()"]');
    const originalText = printBtn.innerHTML;
    printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>লোড হচ্ছে...';
    printBtn.disabled = true;
    
    // Create hidden iframe
    let iframe = document.getElementById('printInvoiceFrame');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'printInvoiceFrame';
        iframe.style.cssText = 'position: absolute; width: 0; height: 0; border: none; visibility: hidden;';
        document.body.appendChild(iframe);
    }
    
    // Load booking page in iframe
    iframe.src = `/admin/bookings/${currentBookingId}`;
    
    iframe.onload = function() {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            // Wait for content to fully render then trigger print
            setTimeout(() => {
                // Add print-invoice class to iframe body
                iframeDoc.body.classList.add('print-invoice');
                
                // Show invoice area
                const invoiceArea = iframeDoc.getElementById('invoice-print-area');
                if (invoiceArea) {
                    invoiceArea.style.display = 'block';
                }
                
                // Print iframe
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                
                // Reset button
                printBtn.innerHTML = originalText;
                printBtn.disabled = false;
            }, 1000);
        } catch (e) {
            console.error('Print error:', e);
            printBtn.innerHTML = originalText;
            printBtn.disabled = false;
            alert('প্রিন্ট করতে সমস্যা হয়েছে');
        }
    };
    
    iframe.onerror = function() {
        printBtn.innerHTML = originalText;
        printBtn.disabled = false;
        alert('ইনভয়েস লোড করতে সমস্যা হয়েছে');
    };
}

// Close modal on outside click
document.getElementById('guestInfoModal').addEventListener('click', function(e) {
    if (e.target === this) closeGuestInfoModal();
});
</script>
@endsection
