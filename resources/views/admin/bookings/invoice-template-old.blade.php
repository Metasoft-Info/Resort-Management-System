<!-- Print-only Invoice Template -->
<div id="invoice-print-area" class="hidden print:block">
 <div class="bg-white p-4 text-xs">
 <!-- Header -->
 <div class="text-center border-b-2 border-green-800 pb-4 mb-6">
 @if($resortInfo && $resortInfo->header_logo)
 <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-16 mx-auto mb-2">
 @else
 <h1 class="text-4xl font-bold text-primary-800 mb-2">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</h1>
 @endif
 @if($resortInfo && ($resortInfo->tagline || $resortInfo->resort_tagline))
 <p class="text-gray-600 text-sm">{{ $resortInfo->tagline ?? $resortInfo->resort_tagline }}</p>
 @endif
 <p class="text-gray-500 text-sm mt-2">
 @if($resortInfo)
 Phone: {{ $resortInfo->phone ?? 'N/A' }} | Email: {{ $resortInfo->email ?? 'N/A' }}
 @if($resortInfo->address)<br>{{ $resortInfo->address }}@endif
 @else
 Phone: +880 1234 567890 | Email: info@tufanresort.com
 @endif
 </p>
 </div>

 <!-- Invoice Info -->
 <div class="flex justify-between mb-6">
 <div>
 <h2 class="text-2xl font-bold text-gray-800 mb-2">BOOKING INVOICE</h2>
 <p class="text-sm text-gray-600">Invoice #: BOOKING-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
 <p class="text-sm text-gray-600">Date: {{ $booking->created_at->format('d/m/Y') }}</p>
 </div>
 <div class="text-right">
 <div class="inline-block px-4 py-2 rounded-lg font-bold text-sm
 @if($booking->status === 'confirmed') bg-primary-100 text-primary-800
 @elseif($booking->status === 'checked_in') bg-primary-100 text-primary-800
 @elseif($booking->status === 'checked_out') bg-gray-100 text-gray-800
 @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
 @else bg-yellow-100 text-yellow-800
 @endif">
 {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
 </div>
 </div>
 </div>

 <!-- Customer Details -->
 <div class="grid grid-cols-2 gap-6 mb-6">
 <div class="border border-gray-300 rounded p-4">
 <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Guest Information</h3>
 <div class="space-y-2 text-sm">
 <p><span class="font-semibold">Name:</span> {{ $booking->customer_name }}</p>
 <p><span class="font-semibold">NID:</span> {{ $booking->customer_nid }}</p>
 <p><span class="font-semibold">Phone:</span> {{ $booking->customer_phone }}</p>
 @if($booking->customer_whatsapp)
 <p><span class="font-semibold">WhatsApp:</span> {{ $booking->customer_whatsapp }}</p>
 @endif
 <p><span class="font-semibold">Email:</span> {{ $booking->customer_email }}</p>
 @if($booking->customer_address)
 <p><span class="font-semibold">Address:</span> {{ $booking->customer_address }}</p>
 @endif
 </div>
 </div>

 <div class="border border-gray-300 rounded p-4">
 <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Booking Details</h3>
 <div class="space-y-2 text-sm">
 <p><span class="font-semibold">Room:</span> {{ $booking->room->room_number }} - {{ $booking->room->roomType->name ?? 'N/A' }}</p>
 <p><span class="font-semibold">Type:</span> {{ $booking->room->roomType->name ?? 'N/A' }}</p>
 <p><span class="font-semibold">Check-In:</span> {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }} @if($booking->check_in_time) at {{ \Carbon\Carbon::parse($booking->check_in_time)->format('h:i A') }} @endif</p>
 <p><span class="font-semibold">Check-Out:</span> {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }} @if($booking->check_out_time) at {{ \Carbon\Carbon::parse($booking->check_out_time)->format('h:i A') }} @endif</p>
 <p><span class="font-semibold">Total Guests:</span> {{ $booking->number_of_guests }}</p>
 <p><span class="font-semibold">Total Nights:</span> {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }}</p>
 </div>
 </div>
 </div>

 <!-- Additional Guests -->
 @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
 <div class="border border-gray-300 rounded p-4 mb-6">
 <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Additional Guest Members</h3>
 <div class="grid grid-cols-2 gap-3">
 @foreach($booking->additionalGuests as $index => $guest)
 <div class="bg-gray-50 p-3 rounded text-sm">
 <p class="font-semibold text-gray-800">{{ $index + 2 }}. {{ $guest->name }}</p>
 <p class="text-gray-600">NID: {{ $guest->nid }}</p>
 <p class="text-gray-600">Phone: {{ $guest->phone }}</p>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 <!-- Billing Details -->
 @php
 $invoiceNights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
 $invoiceBaseAmount = $booking->getCalculatedTotal();
 $invoiceDiscountAmount = 0;
 
 if($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
 $invoiceDiscountAmount = ($invoiceBaseAmount * $booking->discount_percentage) / 100;
 } elseif($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
 $invoiceDiscountAmount = $booking->discount_amount;
 }
 
 $invoiceAfterDiscount = $invoiceBaseAmount - $invoiceDiscountAmount;
 $invoiceExtraCharges = $booking->extra_charges ?? 0;
 $invoiceVatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
 $invoiceGrandTotal = $invoiceAfterDiscount + $invoiceExtraCharges + $invoiceVatAmount;
 @endphp

 <div class="border border-gray-300 rounded p-4 mb-6">
 <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Billing Summary</h3>
 <table class="w-full text-sm">
 <thead class="bg-gray-50">
 <tr>
 <th class="text-left p-2 border-b font-semibold">Description</th>
 <th class="text-center p-2 border-b font-semibold">Quantity</th>
 <th class="text-right p-2 border-b font-semibold">Rate</th>
 <th class="text-right p-2 border-b font-semibold">Amount</th>
 </tr>
 </thead>
 <tbody>
 <tr>
 <td class="p-2 border-b">Room Booking ({{ $booking->room->room_number }} - {{ $booking->room->roomType->name ?? 'N/A' }})</td>
 <td class="text-center p-2 border-b">{{ $invoiceNights }} night(s)</td>
 <td class="text-right p-2 border-b">{{ number_format($booking->room->roomType->price_per_night ?? $booking->room->price_per_night, 2) }}</td>
 <td class="text-right p-2 border-b">{{ number_format($invoiceBaseAmount, 2) }}</td>
 </tr>
 @if($invoiceDiscountAmount > 0)
 <tr class="text-red-600">
 <td class="p-2 border-b">
 Discount @if($booking->discount_type === 'percentage') ({{ $booking->discount_percentage }}%) @else (Flat) @endif
 </td>
 <td class="text-center p-2 border-b">-</td>
 <td class="text-right p-2 border-b">-</td>
 <td class="text-right p-2 border-b">- {{ number_format($invoiceDiscountAmount, 2) }}</td>
 </tr>
 @endif
 @if($invoiceVatAmount > 0)
 <tr>
 <td class="p-2 border-b">VAT (15%)</td>
 <td class="text-center p-2 border-b">-</td>
 <td class="text-right p-2 border-b">-</td>
 <td class="text-right p-2 border-b">{{ number_format($invoiceVatAmount, 2) }}</td>
 </tr>
 @endif
 @if($invoiceExtraCharges > 0)
 <tr>
 <td class="p-2 border-b">
 <div>Additional Charges</div>
 @if($booking->extra_charges_description)
 <div class="text-xs text-gray-600 italic">{{ $booking->extra_charges_description }}</div>
 @endif
 </td>
 <td class="text-center p-2 border-b">-</td>
 <td class="text-right p-2 border-b">-</td>
 <td class="text-right p-2 border-b">{{ number_format($invoiceExtraCharges, 2) }}</td>
 </tr>
 @endif
 <tr class="font-bold bg-gray-50">
 <td colspan="3" class="p-2 text-right">Grand Total:</td>
 <td class="text-right p-2">{{ number_format($invoiceGrandTotal, 2) }}</td>
 </tr>
 <tr class="text-primary-600 font-semibold">
 <td colspan="3" class="p-2 text-right">Advance Payment:</td>
 <td class="text-right p-2">{{ number_format($booking->advance_payment, 2) }}</td>
 </tr>
 <tr class="text-red-600 font-bold">
 <td colspan="3" class="p-2 text-right">Remaining Payment:</td>
 <td class="text-right p-2">{{ number_format($booking->getCalculatedRemaining(), 2) }}</td>
 </tr>
 </tbody>
 </table>
 </div>

 <!-- Payment History -->
 @if($booking->payments && $booking->payments->count() > 0)
 <div class="border border-gray-300 rounded p-4 mb-6">
 <h3 class="font-bold text-gray-800 mb-3 text-sm border-b pb-2">Payment History</h3>
 <table class="w-full text-sm">
 <thead class="bg-gray-50">
 <tr>
 <th class="text-left p-2 border-b">Date</th>
 <th class="text-left p-2 border-b">Type</th>
 <th class="text-left p-2 border-b">Method</th>
 <th class="text-right p-2 border-b">Amount</th>
 <th class="text-left p-2 border-b">Recorded By</th>
 </tr>
 </thead>
 <tbody>
 @foreach($booking->payments as $payment)
 <tr>
 <td class="p-2 border-b">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
 <td class="p-2 border-b">
 <span class="px-2 py-1 rounded text-xs font-semibold
 @if($payment->type === 'advance') bg-primary-100 text-primary-800
 @elseif($payment->type === 'payment') bg-primary-100 text-primary-800
 @else bg-red-100 text-red-800
 @endif">
 {{ ucfirst($payment->type) }}
 </span>
 </td>
 <td class="p-2 border-b uppercase">{{ $payment->method }}</td>
 <td class="p-2 border-b text-right font-semibold
 @if($payment->type === 'refund') text-red-600 @else text-primary-600 @endif">
 @if($payment->type === 'refund') - @endif{{ number_format($payment->amount, 2) }}
 </td>
 <td class="p-2 border-b">{{ $payment->recordedBy->name ?? 'N/A' }}</td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 @endif

 <!-- Payment Info -->
 <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
 <div class="border border-gray-300 rounded p-3">
 <p class="font-semibold text-gray-700">Payment Method:</p>
 <p class="text-gray-900 uppercase">{{ $booking->payment_method }}</p>
 </div>
 <div class="border border-gray-300 rounded p-3">
 <p class="font-semibold text-gray-700">Payment Status:</p>
 <p class="uppercase font-semibold
 @if($booking->payment_status === 'paid') text-primary-600
 @elseif($booking->payment_status === 'partial') text-yellow-600
 @else text-red-600
 @endif">
 {{ $booking->payment_status }}
 </p>
 </div>
 </div>

 <!-- Footer -->
 <div class="text-center mt-4 pt-3 border-t border-gray-300">
 <p class="text-sm text-gray-700 font-medium">Thank you for choosing {{ $resortInfo->resort_name ?? 'our resort' }}!</p>
 <p class="text-xs text-gray-500 mt-2">{{ $resortInfo->footer_text ?? 'We look forward to serving you again.' }}</p>
 <p class="text-xs text-gray-400 mt-3 pt-2 border-t border-gray-200">Developed by Mir Javed Jeetu | Contact: 01811480222</p>
 </div>
 </div>
</div>

<style>
@media print {
 @page {
 size: A4;
 margin: 10mm;
 }
 body {
 font-size: 10px !important;
 }
 body * {
 visibility: hidden;
 }
 #invoice-print-area, #invoice-print-area * {
 visibility: visible;
 }
 #invoice-print-area {
 position: absolute;
 left: 0;
 top: 0;
 width: 100%;
 font-size: 10px !important;
 }
 #invoice-print-area .p-4 {
 padding: 8px !important;
 }
 #invoice-print-area .mb-6 {
 margin-bottom: 8px !important;
 }
 #invoice-print-area .mb-4 {
 margin-bottom: 6px !important;
 }
 #invoice-print-area .p-2 {
 padding: 4px !important;
 }
 #invoice-print-area h1 {
 font-size: 24px !important;
 }
 #invoice-print-area h2 {
 font-size: 16px !important;
 }
 #invoice-print-area h3 {
 font-size: 12px !important;
 }
 .print\:block {
 display: block !important;
 }
 .print\:hidden {
 display: none !important;
 }
}
</style>
