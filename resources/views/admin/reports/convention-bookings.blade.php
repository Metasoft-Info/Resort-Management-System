@extends('layouts.admin')
@section('content')
<div class="p-6">
    @include('admin.reports.partials.shared-header', [
        'title' => 'কনভেনশন বুকিং রিপোর্ট',
        'subtitle' => 'হল বুকিং, পেমেন্ট ও বকেয়ার সারাংশ'
    ])
    @include('admin.reports.partials.shared-styles')

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.reports.convention-bookings') }}">
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
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>সম্পন্ন</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>বাতিল</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">পেমেন্ট স্ট্যাটাস</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>পরিশোধিত</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>আংশিক</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>বকেয়া</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">খুঁজুন</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="নাম / ফোন / প্রতিষ্ঠান" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                        <i class="fas fa-filter mr-2"></i>ফিল্টার
                    </button>
                    <a href="{{ route('admin.reports.convention-bookings') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">হল নির্বাচন</label>
                    <select name="hall_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব হল</option>
                        @foreach($halls as $hall)
                        <option value="{{ $hall->id }}" {{ request('hall_id') == $hall->id ? 'selected' : '' }}>{{ $hall->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">সময় স্লট</label>
                    <select name="time_slot" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">সব সময়</option>
                        <option value="morning" {{ request('time_slot') == 'morning' ? 'selected' : '' }}>সকাল</option>
                        <option value="night" {{ request('time_slot') == 'night' ? 'selected' : '' }}>রাত</option>
                        <option value="full_day" {{ request('time_slot') == 'full_day' ? 'selected' : '' }}>পুরো দিন</option>
                    </select>
                </div>
            </div>
        </form>
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
            <p class="text-gray-600 text-xs">VAT</p>
            <p class="text-xl font-bold text-primary-700 print:text-base">৳{{ number_format($bookings->sum('vat_amount'), 0) }}</p>
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
        <div class="report-table-container">
            <table class="report-table-wide text-sm border border-gray-400">
                <thead>
                    <tr class="bg-gray-200 print:bg-gray-300">
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">তারিখ</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">মোবাইল নম্বর</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">নাম</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">প্রতিষ্ঠান</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">হল</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">সময়</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">বিল</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">বিল জমা</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">VAT</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 text-right whitespace-nowrap">বাকি</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 whitespace-nowrap">পেমেন্ট</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800">মন্তব্য</th>
                        <th class="border border-gray-400 px-2 py-2 text-xs font-bold text-gray-800 print:hidden whitespace-nowrap">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-400 px-2 py-1">{{ \Carbon\Carbon::parse($booking->event_date)->format('d-m-Y') }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->customer_phone }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-medium">{{ $booking->customer_name }}</td>
                        <td class="border border-gray-400 px-2 py-1">{{ $booking->organization_name ?? '-' }}</td>
                        <td class="border border-gray-400 px-2 py-1 font-semibold text-primary-700">{{ $booking->conventionHall->name ?? 'N/A' }}</td>
                        <td class="border border-gray-400 px-2 py-1">
                            @if($booking->time_slot == 'morning') সকাল
                            @elseif($booking->time_slot == 'night') রাত
                            @elseif($booking->time_slot == 'full_day') পুরো দিন
                            @else {{ $booking->time_slot }}
                            @endif
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-right font-semibold">{{ number_format($booking->total_amount, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-primary-600">{{ number_format($booking->advance_payment, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right">{{ number_format($booking->vat_amount ?? 0, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-right text-red-600 font-semibold">{{ number_format($booking->remaining_payment, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center">
                            <span class="px-1 py-0.5 rounded text-xs font-semibold
                                @if($booking->payment_status == 'paid') bg-green-100 text-green-800
                                @elseif($booking->payment_status == 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $booking->payment_status == 'paid' ? 'পরিশোধিত' : ($booking->payment_status == 'partial' ? 'আংশিক' : 'বকেয়া') }}
                            </span>
                        </td>
                        <td class="border border-gray-400 px-2 py-1 text-xs">{{ $booking->notes ?? '' }}</td>
                        <td class="border border-gray-400 px-2 py-1 text-center print:hidden">
                            <button onclick="showConventionInfo({{ $booking->id }})" class="bg-primary-600 text-white px-2 py-1 rounded text-xs hover:bg-primary-700">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="13" class="border border-gray-400 px-4 py-8 text-center text-gray-500">কোনো বুকিং পাওয়া যায়নি</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-200 font-bold">
                        <td colspan="6" class="border border-gray-400 px-2 py-2 text-right">মোট:</td>
                        <td class="border border-gray-400 px-2 py-2 text-right">{{ number_format($totalRevenue, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-primary-700">{{ number_format($totalAdvance, 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right">{{ number_format($bookings->sum('vat_amount'), 0) }}</td>
                        <td class="border border-gray-400 px-2 py-2 text-right text-red-600">{{ number_format($totalRemaining, 0) }}</td>
                        <td colspan="2" class="border border-gray-400 px-2 py-2"></td>
                        <td class="border border-gray-400 px-2 py-2 print:hidden"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="mt-6 print:hidden">{{ $bookings->links() }}</div>

    @include('admin.reports.partials.shared-footer')
</div>

<!-- Convention Info Modal -->
<div id="conventionInfoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl my-8 mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
            <h3 class="text-xl font-bold text-gray-800"><i class="fas fa-file-invoice mr-2 text-primary-600"></i>কনভেনশন বুকিং বিবরণী</h3>
            <div class="flex gap-2">
                <button onclick="printConventionInvoice()" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                    <i class="fas fa-print mr-1"></i>ইনভয়েস প্রিন্ট
                </button>
                <button onclick="closeConventionInfoModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div id="conventionInfoContent" class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-primary-600"></i>
                <p class="text-gray-600 mt-2">লোড হচ্ছে...</p>
            </div>
        </div>
    </div>
</div>

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
    body.print-convention-info * {
        visibility: hidden;
    }
    body.print-convention-info #conventionPrintArea,
    body.print-convention-info #conventionPrintArea * {
        visibility: visible;
    }
    body.print-convention-info #conventionPrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 15mm;
    }
    body.print-convention-info .invoice-print {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12pt;
    }
    body.print-convention-info .invoice-print table {
        font-size: 10pt !important;
    }
    body.print-convention-info .invoice-print th,
    body.print-convention-info .invoice-print td {
        padding: 6px 10px !important;
    }
    @page {
        size: A4 portrait;
        margin: 10mm;
    }
}
</style>

<script>
// Store current booking ID for printing
let currentConventionId = null;

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
    return '৳' + parseFloat(amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

// Helper function to get status badge
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">পেন্ডিং</span>',
        'confirmed': '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">নিশ্চিত</span>',
        'completed': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">সম্পন্ন</span>',
        'cancelled': '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">বাতিল</span>',
        'paid': '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">পরিশোধিত</span>',
        'partial': '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">আংশিক</span>',
        'unpaid': '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">বকেয়া</span>'
    };
    return badges[status] || `<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">${status}</span>`;
}

// Helper function to get time slot label
function getTimeSlot(slot) {
    const slots = {
        'morning': 'সকাল (8AM-2PM)',
        'night': 'রাত (6PM-11PM)',
        'full_day': 'পুরো দিন (8AM-11PM)'
    };
    return slots[slot] || slot;
}

function showConventionInfo(bookingId) {
    const modal = document.getElementById('conventionInfoModal');
    const content = document.getElementById('conventionInfoContent');
    
    // Store current booking ID for printing
    currentConventionId = bookingId;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    // Fetch booking details
    fetch(`/admin/convention-bookings/${bookingId}?ajax=1`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.booking) {
            const b = data.booking;
            
            // Build services list
            let servicesHtml = '';
            let servicesTableHtml = '';
            
            // Hall rent
            servicesTableHtml = `
                <tr class="border-b">
                    <td class="py-2 px-2">হল ভাড়া (${b.convention_hall?.name || 'N/A'})</td>
                    <td class="py-2 px-2 text-right">1</td>
                    <td class="py-2 px-2 text-right font-semibold">${formatTaka(b.hall_rent)}</td>
                </tr>
            `;
            
            // Food package
            if (b.food_cost > 0 && b.food_package) {
                servicesTableHtml += `
                    <tr class="border-b">
                        <td class="py-2 px-2">ফুড প্যাকেজ: ${b.food_package.name} (${b.number_of_guests} জন)</td>
                        <td class="py-2 px-2 text-right">${b.number_of_guests}</td>
                        <td class="py-2 px-2 text-right font-semibold">${formatTaka(b.food_cost)}</td>
                    </tr>
                `;
            }
            
            // Addon services
            if (b.addons_cost > 0) {
                servicesTableHtml += `
                    <tr class="border-b">
                        <td class="py-2 px-2">অ্যাডঅন সার্ভিস</td>
                        <td class="py-2 px-2 text-right">-</td>
                        <td class="py-2 px-2 text-right font-semibold">${formatTaka(b.addons_cost)}</td>
                    </tr>
                `;
            }
            
            // Calculate totals
            const subtotal = parseFloat(b.hall_rent || 0) + parseFloat(b.food_cost || 0) + parseFloat(b.addons_cost || 0);
            const discount = parseFloat(b.discount) || 0;
            const vatAmount = parseFloat(b.vat_amount) || 0;
            const grandTotal = parseFloat(b.total_amount) || 0;
            const advancePayment = parseFloat(b.advance_payment) || 0;
            const remaining = parseFloat(b.remaining_payment) || 0;
            
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
                                        <td class="py-2 px-2">${formatDate(p.payment_date)}</td>
                                        <td class="py-2 px-2 text-right font-semibold text-green-600">${formatTaka(p.amount)}</td>
                                        <td class="py-2 px-2">${p.payment_method || 'N/A'}</td>
                                        <td class="py-2 px-2 text-xs text-gray-600">${p.notes || '-'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
            
            content.innerHTML = `
                <div class="space-y-4" id="conventionInfoPrintContent">
                    <!-- Header -->
                    <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
                        <h2 class="text-xl font-bold text-gray-800">তুফান কনভেনশন রিসোর্ট</h2>
                        <p class="text-sm text-gray-600">কনভেনশন বুকিং বিবরণী</p>
                        <p class="text-lg font-bold text-primary-600 mt-2">Booking #CONV-${String(b.id).padStart(5, '0')}</p>
                    </div>
                    
                    <!-- Customer & Event Info Side by Side -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Info -->
                        <div class="bg-primary-50 p-4 rounded-lg border border-primary-200">
                            <h4 class="font-bold text-primary-800 mb-3 border-b pb-2"><i class="fas fa-user mr-2"></i>গ্রাহক তথ্য</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-semibold w-24 inline-block">নাম:</span> ${b.customer_name || 'N/A'}</p>
                                <p><span class="font-semibold w-24 inline-block">ফোন:</span> ${b.customer_phone || 'N/A'}</p>
                                <p><span class="font-semibold w-24 inline-block">প্রতিষ্ঠান:</span> ${b.organization_name || 'N/A'}</p>
                                <p><span class="font-semibold w-24 inline-block">ইমেইল:</span> ${b.customer_email || 'N/A'}</p>
                                <p><span class="font-semibold w-24 inline-block">ঠিকানা:</span> ${b.customer_address || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <!-- Event Info -->
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fas fa-calendar-alt mr-2"></i>ইভেন্ট তথ্য</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-semibold">হল:</span> ${b.convention_hall?.name || 'N/A'}</p>
                                <p><span class="font-semibold">ইভেন্ট তারিখ:</span> ${formatDate(b.event_date)}</p>
                                <p><span class="font-semibold">সময়:</span> ${getTimeSlot(b.time_slot)}</p>
                                <p><span class="font-semibold">ইভেন্ট টাইপ:</span> ${b.event_type || 'N/A'}</p>
                                <p><span class="font-semibold">অতিথি সংখ্যা:</span> ${b.number_of_guests || 0} জন</p>
                                <p><span class="font-semibold">স্ট্যাটাস:</span> ${getStatusBadge(b.status)}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Services Table -->
                    <div class="mt-4">
                        <h4 class="font-bold text-gray-800 mb-2"><i class="fas fa-concierge-bell mr-2"></i>সার্ভিস বিবরণ</h4>
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-2 text-left border">বিবরণ</th>
                                    <th class="py-2 px-2 text-right border">পরিমাণ</th>
                                    <th class="py-2 px-2 text-right border">মোট</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${servicesTableHtml}
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="2" class="py-2 px-2 text-right border">সাবটোটাল:</td>
                                    <td class="py-2 px-2 text-right border">${formatTaka(subtotal)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Payment Summary -->
                    <div class="mt-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h4 class="font-bold text-gray-800 mb-3 border-b pb-2"><i class="fas fa-calculator mr-2"></i>পেমেন্ট সারাংশ</h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>সাবটোটাল:</div>
                            <div class="text-right">${formatTaka(subtotal)}</div>
                            
                            ${discount > 0 ? `
                            <div class="text-green-600">ডিসকাউন্ট:</div>
                            <div class="text-right text-green-600">- ${formatTaka(discount)}</div>
                            ` : ''}
                            
                            ${vatAmount > 0 ? `
                            <div>VAT (${b.vat_percentage || 15}%):</div>
                            <div class="text-right">+ ${formatTaka(vatAmount)}</div>
                            ` : ''}
                            
                            <div class="font-bold text-lg border-t pt-2 mt-2">সর্বমোট:</div>
                            <div class="text-right font-bold text-lg border-t pt-2 mt-2">${formatTaka(grandTotal)}</div>
                            
                            <div class="text-green-700">মোট জমা:</div>
                            <div class="text-right text-green-700">${formatTaka(advancePayment)}</div>
                            
                            <div class="font-bold ${remaining > 0 ? 'text-red-600' : 'text-green-600'}">বাকি:</div>
                            <div class="text-right font-bold ${remaining > 0 ? 'text-red-600' : 'text-green-600'}">${formatTaka(remaining)}</div>
                        </div>
                    </div>
                    
                    ${paymentHistoryHtml}
                    
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

function closeConventionInfoModal() {
    const modal = document.getElementById('conventionInfoModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function printConventionInvoice() {
    if (!currentConventionId) {
        alert('বুকিং তথ্য পাওয়া যায়নি');
        return;
    }
    
    // Show loading indicator
    const printBtn = document.querySelector('button[onclick="printConventionInvoice()"]');
    const originalText = printBtn.innerHTML;
    printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>লোড হচ্ছে...';
    printBtn.disabled = true;
    
    // Create hidden iframe
    let iframe = document.getElementById('printConventionFrame');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'printConventionFrame';
        iframe.style.cssText = 'position: absolute; width: 0; height: 0; border: none; visibility: hidden;';
        document.body.appendChild(iframe);
    }
    
    // Load booking page in iframe
    iframe.src = `/admin/convention-bookings/${currentConventionId}`;
    
    iframe.onload = function() {
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            
            // Wait for content to fully render then trigger print
            setTimeout(() => {
                // Add print class to iframe body
                iframeDoc.body.classList.add('print-convention-invoice');
                
                // Show invoice area
                const invoiceArea = iframeDoc.getElementById('convention-invoice-print-area');
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
document.getElementById('conventionInfoModal').addEventListener('click', function(e) {
    if (e.target === this) closeConventionInfoModal();
});
</script>
@endsection
