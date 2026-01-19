@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Print-only content -->
    <div id="invoice-print-area" class="hidden print:block">
        <div class="bg-white p-8 text-sm">
            <!-- Top Developer Info -->
            <div class="text-center mb-4 pb-3 border-b border-gray-200">
                <p class="text-xs text-gray-600 mb-1">Thank you for choosing Tufan Resort!</p>
                <p class="text-xs text-gray-700 font-semibold">Developed By Mir Javed Jeetu</p>
                <p class="text-xs text-gray-600">Contact: 01811480222</p>
            </div>

            <!-- Header -->
            <div class="text-center border-b-2 border-green-800 pb-4 mb-6">
                <h1 class="text-4xl font-bold text-green-800 mb-2">Tufan Resort</h1>
                <p class="text-gray-600 text-sm">🏞️ Lake View Resort & Convention Center</p>
                <p class="text-gray-500 text-sm mt-2">
                    Phone: +880 1234 567890 | Email: info@tufanresort.com
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
                        @if($booking->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($booking->status === 'checked_in') bg-green-100 text-green-800
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
                        <p><span class="font-semibold">Room:</span> {{ $booking->room->room_number }} - {{ $booking->room->roomType->name }}</p>
                        <p><span class="font-semibold">Type:</span> {{ ucfirst($booking->room->roomType->name) }}</p>
                        <p><span class="font-semibold">Check-In:</span> {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }} @if($booking->check_in_time) at {{ $booking->check_in_time }} @endif</p>
                        <p><span class="font-semibold">Check-Out:</span> {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }} @if($booking->check_out_time) at {{ $booking->check_out_time }} @endif</p>
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
                $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
                $baseAmount = $booking->total_amount;
                $discountAmount = 0;
                
                if($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
                    $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
                } elseif($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
                    $discountAmount = $booking->discount_amount;
                }
                
                $afterDiscount = $baseAmount - $discountAmount;
                $extraCharges = $booking->extra_charges ?? 0;
                $vatAmount = ($booking->vat_enabled && $booking->vat_amount) ? $booking->vat_amount : 0;
                $grandTotal = $afterDiscount + $extraCharges + $vatAmount;
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
                            <td class="p-2 border-b">Room Booking ({{ $booking->room->room_number }} - {{ $booking->room->roomType->name }})</td>
                            <td class="text-center p-2 border-b">{{ $nights }} night(s)</td>
                            <td class="text-right p-2 border-b">৳{{ number_format($booking->room->roomType->price_per_night, 2) }}</td>
                            <td class="text-right p-2 border-b">৳{{ number_format($baseAmount, 2) }}</td>
                        </tr>
                        @if($discountAmount > 0)
                        <tr class="text-red-600">
                            <td class="p-2 border-b">
                                Discount @if($booking->discount_type === 'percentage') ({{ $booking->discount_percentage }}%) @else (Flat) @endif
                            </td>
                            <td class="text-center p-2 border-b">-</td>
                            <td class="text-right p-2 border-b">-</td>
                            <td class="text-right p-2 border-b">- ৳{{ number_format($discountAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($vatAmount > 0)
                        <tr>
                            <td class="p-2 border-b">VAT (15%)</td>
                            <td class="text-center p-2 border-b">-</td>
                            <td class="text-right p-2 border-b">-</td>
                            <td class="text-right p-2 border-b">৳{{ number_format($vatAmount, 2) }}</td>
                        </tr>
                        @endif
                        @if($extraCharges > 0)
                        <tr>
                            <td class="p-2 border-b">
                                <div>Additional Charges</div>
                                @if($booking->extra_charges_description)
                                <div class="text-xs text-gray-600 italic">{{ $booking->extra_charges_description }}</div>
                                @endif
                            </td>
                            <td class="text-center p-2 border-b">-</td>
                            <td class="text-right p-2 border-b">-</td>
                            <td class="text-right p-2 border-b">৳{{ number_format($extraCharges, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="font-bold bg-gray-50">
                            <td colspan="3" class="p-2 text-right">Grand Total:</td>
                            <td class="text-right p-2">৳{{ number_format($grandTotal, 2) }}</td>
                        </tr>
                        <tr class="text-green-600 font-semibold">
                            <td colspan="3" class="p-2 text-right">Advance Payment:</td>
                            <td class="text-right p-2">৳{{ number_format($booking->advance_payment, 2) }}</td>
                        </tr>
                        <tr class="text-red-600 font-bold">
                            <td colspan="3" class="p-2 text-right">Remaining Payment:</td>
                            <td class="text-right p-2">৳{{ number_format($booking->remaining_payment, 2) }}</td>
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
                                    @if($payment->type === 'advance') bg-blue-100 text-blue-800
                                    @elseif($payment->type === 'payment') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($payment->type) }}
                                </span>
                            </td>
                            <td class="p-2 border-b uppercase">{{ $payment->method }}</td>
                            <td class="p-2 border-b text-right font-semibold
                                @if($payment->type === 'refund') text-red-600 @else text-green-600 @endif">
                                @if($payment->type === 'refund') - @endif৳{{ number_format($payment->amount, 2) }}
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
                        @if($booking->payment_status === 'paid') text-green-600
                        @elseif($booking->payment_status === 'partial') text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $booking->payment_status }}
                    </p>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="border-t border-gray-300 pt-3 mt-4">
                <h4 class="font-bold text-gray-800 mb-2 text-sm">Terms & Conditions:</h4>
                <ul class="text-xs text-gray-600 space-y-1 list-disc list-inside">
                    <li>Check-in time is from 2:00 PM and check-out time is before 11:00 AM</li>
                    <li>Valid photo ID required during check-in</li>
                    <li>Damage to resort property will be charged</li>
                    <li>Cancellation must be done 48 hours before check-in date</li>
                    <li>Outside food and beverages are not allowed</li>
                </ul>
            </div>

            <!-- Footer -->
            <div class="text-center mt-4 pt-3 border-t">
                <p class="text-xs text-gray-600 mb-1">Thank you for choosing Tufan Resort!</p>
                <p class="text-xs text-gray-700 font-semibold">Developed By Mir Javed Jeetu</p>
                <p class="text-xs text-gray-600">Contact: 01811480222</p>
            </div>
        </div>
    </div>

    <!-- Screen-only content -->
    <div class="print:hidden">
        <!-- Existing booking show content will be here -->
    </div>
</div>

<style>
@media print {
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
    }
}
</style>
@endsection
