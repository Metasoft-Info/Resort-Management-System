<!-- Print-only Invoice Template - BILL Format -->
<div id="invoice-print-area" class="hidden print:block">
    <div class="bg-white p-6 text-sm" style="max-height: 100vh; overflow: hidden;">
        
        <!-- Header with Logo -->
        <div class="text-center border-b-2 border-green-700 pb-4 mb-4">
            @if($resortInfo && $resortInfo->header_logo)
                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" class="h-14 mx-auto mb-2">
            @else
                <h1 class="text-3xl font-bold text-primary-800">{{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }}</h1>
            @endif
            <p class="text-gray-600 text-sm">
                @if($resortInfo && $resortInfo->address)
                    {{ $resortInfo->address }}
                @endif
            </p>
            <p class="text-gray-500 text-xs mt-1">
                @if($resortInfo)
                    Phone: {{ $resortInfo->phone ?? 'N/A' }} | Email: {{ $resortInfo->email ?? 'N/A' }}
                @endif
            </p>
        </div>

        <!-- BILL Title -->
        <div class="text-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800 tracking-wider">BILL</h2>
        </div>

        <!-- Guest Info Row -->
        <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
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

        <!-- Additional Guests -->
        @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
        <div class="mb-4 text-sm border border-gray-300 rounded p-2">
            <span class="font-semibold">Accompanying Guests ({{ $booking->additionalGuests->count() }}):</span>
            <div class="grid grid-cols-2 gap-2 mt-1">
                @foreach($booking->additionalGuests as $guest)
                <div class="text-xs">
                    <i class="fas fa-user text-primary-600"></i> {{ $guest->name }}
                    @if($guest->nid) | NID: {{ $guest->nid }}@endif
                    @if($guest->phone) | {{ $guest->phone }}@endif
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

        <table class="w-full border-collapse border border-gray-400 mb-4 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-400 p-2 text-left">Arrival Date</th>
                    <th class="border border-gray-400 p-2 text-left">Departure Date</th>
                    <th class="border border-gray-400 p-2 text-center">Room No.</th>
                    <th class="border border-gray-400 p-2 text-left">Room Name</th>
                    <th class="border border-gray-400 p-2 text-center">Stay</th>
                    <th class="border border-gray-400 p-2 text-right">Room Rent</th>
                    <th class="border border-gray-400 p-2 text-right">Amount</th>
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
                            <td class="border border-gray-400 p-2" rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                            <td class="border border-gray-400 p-2" rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                            @endif
                            <td class="border border-gray-400 p-2 text-center">{{ $room->room_number }}</td>
                            <td class="border border-gray-400 p-2">{{ $room->roomType->name ?? 'Room' }}</td>
                            <td class="border border-gray-400 p-2 text-center">{{ $invoiceNights }} Night(s)</td>
                            <td class="border border-gray-400 p-2 text-right">৳{{ number_format($roomPricePerNight, 0) }}</td>
                            <td class="border border-gray-400 p-2 text-right">৳{{ number_format($roomAmount, 0) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="border border-gray-400 p-2">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                        <td class="border border-gray-400 p-2">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                        <td class="border border-gray-400 p-2 text-center">-</td>
                        <td class="border border-gray-400 p-2">-</td>
                        <td class="border border-gray-400 p-2 text-center">{{ $invoiceNights }} Night(s)</td>
                        <td class="border border-gray-400 p-2 text-right">-</td>
                        <td class="border border-gray-400 p-2 text-right">৳{{ number_format($invoiceBaseAmount, 0) }}</td>
                    </tr>
                @endif
                @if($allRooms->count() > 1)
                <tr class="bg-gray-50">
                    <td colspan="6" class="border border-gray-400 p-2 text-right font-semibold">Room Subtotal ({{ $allRooms->count() }} rooms):</td>
                    <td class="border border-gray-400 p-2 text-right font-semibold">৳{{ number_format($invoiceBaseAmount, 0) }}</td>
                </tr>
                @endif
                @if($invoiceExtraCharges > 0)
                <tr>
                    <td colspan="6" class="border border-gray-400 p-2 text-right">Extra Charges @if($booking->extra_charges_description)<span class="text-xs text-gray-600">({{ $booking->extra_charges_description }})</span>@endif:</td>
                    <td class="border border-gray-400 p-2 text-right">৳{{ number_format($invoiceExtraCharges, 0) }}</td>
                </tr>
                @endif
                @if($invoiceVatAmount > 0)
                <tr>
                    <td colspan="6" class="border border-gray-400 p-2 text-right">VAT (15%):</td>
                    <td class="border border-gray-400 p-2 text-right">৳{{ number_format($invoiceVatAmount, 0) }}</td>
                </tr>
                @endif
                <tr class="bg-gray-50">
                    <td colspan="6" class="border border-gray-400 p-2 text-right font-semibold">Total:</td>
                    <td class="border border-gray-400 p-2 text-right font-semibold">৳{{ number_format($invoiceBaseAmount + $invoiceExtraCharges + $invoiceVatAmount, 0) }}</td>
                </tr>
                @if($invoiceDiscountAmount > 0)
                <tr class="text-red-600">
                    <td colspan="6" class="border border-gray-400 p-2 text-right">Discount @if($booking->discount_type === 'percentage')({{ $booking->discount_percentage }}%)@endif:</td>
                    <td class="border border-gray-400 p-2 text-right">- ৳{{ number_format($invoiceDiscountAmount, 0) }}</td>
                </tr>
                @endif
                <tr class="bg-green-50 font-bold">
                    <td colspan="6" class="border border-gray-400 p-2 text-right">Grand Total:</td>
                    <td class="border border-gray-400 p-2 text-right text-primary-700">৳{{ number_format($invoiceGrandTotal, 0) }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="border border-gray-400 p-2 text-right">Advance Paid:</td>
                    <td class="border border-gray-400 p-2 text-right text-primary-600">৳{{ number_format($booking->advance_payment, 0) }}</td>
                </tr>
                <tr class="font-semibold">
                    <td colspan="6" class="border border-gray-400 p-2 text-right">Due Amount:</td>
                    <td class="border border-gray-400 p-2 text-right text-red-600">৳{{ number_format($booking->remaining_payment, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="grid grid-cols-2 gap-8 mb-4 mt-6">
            <div class="text-center">
                <div class="border-t border-gray-400 pt-2 mt-8">
                    <p class="text-sm">Guest Signature</p>
                </div>
            </div>
            <div class="text-center">
                <div class="border-t border-gray-400 pt-2 mt-8">
                    <p class="text-sm">Authorised Signature</p>
                </div>
            </div>
        </div>

        <!-- Amenities Section -->
        <div class="border border-gray-300 rounded p-3 mb-3">
            <h4 class="font-bold text-gray-800 mb-2 text-sm">Amenities Included:</h4>
            <div class="grid grid-cols-6 gap-2 text-xs text-gray-700">
                <div class="flex items-center"><i class="fas fa-wifi mr-1 text-primary-600"></i> Wi-Fi</div>
                <div class="flex items-center"><i class="fas fa-coffee mr-1 text-primary-600"></i> Breakfast</div>
                <div class="flex items-center"><i class="fas fa-tint mr-1 text-primary-600"></i> Water</div>
                <div class="flex items-center"><i class="fas fa-parking mr-1 text-primary-600"></i> Parking</div>
                <div class="flex items-center"><i class="fas fa-video mr-1 text-primary-600"></i> CCTV</div>
                <div class="flex items-center"><i class="fas fa-concierge-bell mr-1 text-primary-600"></i> Room Service</div>
                <div class="flex items-center"><i class="fas fa-tv mr-1 text-primary-600"></i> LED TV</div>
                <div class="flex items-center"><i class="fas fa-utensils mr-1 text-primary-600"></i> Restaurant</div>
                <div class="flex items-center"><i class="fas fa-shield-alt mr-1 text-primary-600"></i> Security</div>
                <div class="flex items-center"><i class="fas fa-couch mr-1 text-primary-600"></i> Furniture</div>
                <div class="flex items-center"><i class="fas fa-phone mr-1 text-primary-600"></i> Intercom</div>
                <div class="flex items-center"><i class="fas fa-hot-tub mr-1 text-primary-600"></i> Hot Water</div>
            </div>
        </div>

        <!-- Terms & Policy Section -->
        <div class="border border-gray-300 rounded p-3 text-xs">
            <h4 class="font-bold text-gray-800 mb-2">Terms & Conditions:</h4>
            <ul class="list-disc list-inside space-y-1 text-gray-700">
                <li><strong>Check-in/Check-out Time:</strong> 12:00 Hours (Noon)</li>
                <li><strong>Cancellation Policy:</strong> Not applicable after check-in.</li>
                <li><strong>Booking Confirmation:</strong> Full payment required 15 days before journey date.</li>
                <li>Guests are requested to maintain decorum and follow resort rules.</li>
                <li>Management reserves the right to refuse service.</li>
                <li>Any damage to property will be charged to the guest.</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="text-center mt-3 pt-2 border-t border-gray-300">
            <p class="text-sm text-gray-700">Thank you for staying with us!</p>
            <p class="text-xs text-gray-400 mt-1">{{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }} | {{ $resortInfo->phone ?? '' }}</p>
        </div>

        <!-- Developer Credit -->
        <div class="text-center mt-2 pt-2 border-t border-gray-200">
            <p class="text-xs text-gray-400">Developed By <span class="font-semibold">Mir Javed Jeetu</span> | Contact: <span class="font-semibold">01811480222</span></p>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4;
        margin: 8mm;
    }
    body {
        font-size: 11px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
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
    }
    .print\:block {
        display: block !important;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>
