<!-- Print-only Invoice Template - BILL Format -->
<div id="invoice-print-area" class="hidden print:block">
    <div class="invoice-container bg-white text-xs">
        
        <!-- Header with Logo -->
        <div class="text-center border-b-2 border-green-700 pb-2 mb-2">
            @if($resortInfo && $resortInfo->header_logo)
                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-10 mx-auto mb-1">
            @else
                <h1 class="text-xl font-bold text-primary-800">{{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }}</h1>
            @endif
            <p class="text-gray-600 text-xs">
                @if($resortInfo && $resortInfo->address)
                    {{ $resortInfo->address }}
                @endif
            </p>
            <p class="text-gray-500 text-xs">
                @if($resortInfo)
                    Phone: {{ $resortInfo->phone ?? 'N/A' }} | Email: {{ $resortInfo->email ?? 'N/A' }}
                @endif
            </p>
        </div>

        <!-- BILL Title -->
        <div class="text-center mb-2">
            <h2 class="text-lg font-bold text-gray-800 tracking-wider">BILL</h2>
        </div>

        <!-- Guest Info Row - Compact -->
        <div class="grid grid-cols-3 gap-2 mb-2 text-xs">
            <div>
                <span class="font-semibold">Name:</span> {{ $booking->customer_name }}
                @if($booking->company_name)
                    <br><span class="font-semibold">Company:</span> {{ $booking->company_name }}
                @endif
                @if($booking->customer_nid)
                    <br><span class="font-semibold">NID:</span> {{ $booking->customer_nid }}
                @endif
            </div>
            <div>
                <span class="font-semibold">Address:</span> {{ $booking->customer_address ?? 'N/A' }}
                <br><span class="font-semibold">Phone:</span> {{ $booking->customer_phone }}
            </div>
            <div class="text-right">
                <span class="font-semibold">Date:</span> {{ now()->format('d/m/Y') }}
                <br><span class="font-semibold">Bill No:</span> #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <!-- Additional Guests - Compact -->
        @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
        <div class="mb-2 text-xs border border-gray-300 rounded p-1">
            <span class="font-semibold">Accompanying Guests ({{ $booking->additionalGuests->count() }}):</span>
            <div class="grid grid-cols-3 gap-1 mt-1">
                @foreach($booking->additionalGuests as $guest)
                <div class="text-xs">
                    <i class="fas fa-user text-primary-600"></i> {{ $guest->name }}
                    @if($guest->nid) | {{ $guest->nid }}@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Billing Table -->
        @php
            $invoiceNights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
            $invoiceBaseAmount = $booking->total_amount;
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
            $allRooms = $booking->getAllRooms();
            $bookingRooms = $booking->bookingRooms;
        @endphp

        <table class="w-full border-collapse border border-gray-400 mb-2 text-xs invoice-table">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-400 p-1 text-left">Arrival</th>
                    <th class="border border-gray-400 p-1 text-left">Departure</th>
                    <th class="border border-gray-400 p-1 text-center">Room</th>
                    <th class="border border-gray-400 p-1 text-left">Room Type</th>
                    <th class="border border-gray-400 p-1 text-center">Night</th>
                    <th class="border border-gray-400 p-1 text-right">Rate</th>
                    <th class="border border-gray-400 p-1 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($allRooms->count() > 0)
                    @foreach($allRooms as $index => $room)
                        @php
                            // Get price from booking_rooms table if available, otherwise from room/roomType
                            $bookingRoom = $bookingRooms->where('room_id', $room->id)->first();
                            $roomPricePerNight = $bookingRoom ? $bookingRoom->price_per_night : ($room->roomType->price_per_night ?? $room->price_per_night ?? 0);
                            $roomAmount = $invoiceNights * $roomPricePerNight;
                        @endphp
                        <tr>
                            @if($index === 0)
                            <td class="border border-gray-400 p-1" rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                            <td class="border border-gray-400 p-1" rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                            @endif
                            <td class="border border-gray-400 p-1 text-center">{{ $room->room_number }}</td>
                            <td class="border border-gray-400 p-1">{{ $room->roomType->name ?? 'Room' }}</td>
                            <td class="border border-gray-400 p-1 text-center">{{ $invoiceNights }}</td>
                            <td class="border border-gray-400 p-1 text-right">৳{{ number_format($roomPricePerNight, 0) }}</td>
                            <td class="border border-gray-400 p-1 text-right">৳{{ number_format($roomAmount, 0) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="border border-gray-400 p-1">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                        <td class="border border-gray-400 p-1">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                        <td class="border border-gray-400 p-1 text-center">-</td>
                        <td class="border border-gray-400 p-1">-</td>
                        <td class="border border-gray-400 p-1 text-center">{{ $invoiceNights }}</td>
                        <td class="border border-gray-400 p-1 text-right">-</td>
                        <td class="border border-gray-400 p-1 text-right">৳{{ number_format($invoiceBaseAmount, 0) }}</td>
                    </tr>
                @endif
                @if($allRooms->count() > 1)
                <tr class="bg-gray-50">
                    <td colspan="6" class="border border-gray-400 p-1 text-right font-semibold">Room Subtotal:</td>
                    <td class="border border-gray-400 p-1 text-right font-semibold">৳{{ number_format($invoiceBaseAmount, 0) }}</td>
                </tr>
                @endif
                @if($invoiceExtraCharges > 0)
                <tr>
                    <td colspan="6" class="border border-gray-400 p-1 text-right">Extra @if($booking->extra_charges_description)<span class="text-xs">({{ $booking->extra_charges_description }})</span>@endif:</td>
                    <td class="border border-gray-400 p-1 text-right">৳{{ number_format($invoiceExtraCharges, 0) }}</td>
                </tr>
                @endif
                @if($invoiceVatAmount > 0)
                <tr>
                    <td colspan="6" class="border border-gray-400 p-1 text-right">VAT (15%):</td>
                    <td class="border border-gray-400 p-1 text-right">৳{{ number_format($invoiceVatAmount, 0) }}</td>
                </tr>
                @endif
                <tr class="bg-gray-50">
                    <td colspan="6" class="border border-gray-400 p-1 text-right font-semibold">Total:</td>
                    <td class="border border-gray-400 p-1 text-right font-semibold">৳{{ number_format($invoiceBaseAmount + $invoiceExtraCharges + $invoiceVatAmount, 0) }}</td>
                </tr>
                @if($invoiceDiscountAmount > 0)
                <tr class="text-red-600">
                    <td colspan="6" class="border border-gray-400 p-1 text-right">Discount @if($booking->discount_type === 'percentage')({{ $booking->discount_percentage }}%)@endif:</td>
                    <td class="border border-gray-400 p-1 text-right">- ৳{{ number_format($invoiceDiscountAmount, 0) }}</td>
                </tr>
                @endif
                <tr class="bg-green-50 font-bold">
                    <td colspan="6" class="border border-gray-400 p-1 text-right">Grand Total:</td>
                    <td class="border border-gray-400 p-1 text-right text-primary-700">৳{{ number_format($invoiceGrandTotal, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="border border-gray-400 p-1 text-right">Advance Paid:</td>
                    <td class="border border-gray-400 p-1 text-right text-primary-600">৳{{ number_format($booking->advance_payment, 0) }}</td>
                </tr>
                <tr class="font-semibold">
                    <td colspan="6" class="border border-gray-400 p-1 text-right">Due Amount:</td>
                    <td class="border border-gray-400 p-1 text-right text-red-600">৳{{ number_format($booking->remaining_payment, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signature Section - Compact -->
        <div class="grid grid-cols-2 gap-4 mb-1 mt-2">
            <div class="text-center">
                <div class="border-t border-gray-400 pt-1 mt-4">
                    <p class="text-xs">Guest Signature</p>
                </div>
            </div>
            <div class="text-center">
                <div class="border-t border-gray-400 pt-1 mt-4">
                    <p class="text-xs">Authorised Signature</p>
                </div>
            </div>
        </div>

        <!-- Amenities Section - Single Line -->
        <div class="border border-gray-300 rounded p-1 mb-1">
            <span class="font-bold text-gray-800 text-xs">Amenities:</span>
            <span class="text-xs text-gray-700">Wi-Fi, Breakfast, Water, Parking, CCTV, Room Service, LED TV, Restaurant, Security, Hot Water</span>
        </div>

        <!-- Terms - Ultra Compact -->
        <div class="border border-gray-300 rounded p-1 text-xs">
            <span class="font-bold text-gray-800">Terms:</span>
            <span class="text-gray-700">Check-in/out: 12:00 Noon | Cancellation not applicable after check-in | Full payment required 15 days before journey | Damage charges apply</span>
        </div>

        <!-- Footer - Compact -->
        <div class="text-center mt-1 pt-1 border-t border-gray-300">
            <p class="text-xs text-gray-700">Thank you for staying with us! | {{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }} | {{ $resortInfo->phone ?? '' }}</p>
            <p class="text-xs text-gray-400">Developed By Mir Javed Jeetu | 01811480222</p>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 5mm;
    }
    
    html, body {
        font-size: 9px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        margin: 0;
        padding: 0;
        height: auto !important;
        overflow: visible !important;
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
        height: auto !important;
        overflow: visible !important;
    }
    
    .invoice-container {
        padding: 3mm;
        width: 100%;
        height: auto !important;
        overflow: visible !important;
    }
    
    .invoice-table {
        page-break-inside: auto;
    }
    
    .invoice-table tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    .print\:block {
        display: block !important;
    }
    
    .print\:hidden {
        display: none !important;
    }
}
</style>
