<!-- Print-only Reservation Letter Template -->
<div id="reservation-print-area" class="hidden">
 <div class="reservation-container bg-white" style="font-family: 'Times New Roman', Times, serif; font-size: 13px; padding: 10mm 15mm; max-width: 210mm; margin: 0 auto;">
 
 <!-- Header with Logo - Centered -->
 <div style="text-align: center; margin-bottom: 10px;">
 @php $logoPath = ($resortInfo && $resortInfo->header_logo) ? public_path('storage/' . $resortInfo->header_logo) : null; @endphp
 @if($logoPath && file_exists($logoPath))
 <img src="{{ $logoPath }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" style="height: 65px; margin: 0 auto 5px; display: block;">
 @else
 <div style="width: 65px; height: 65px; border: 2px solid #000; border-radius: 50%; margin: 0 auto 5px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold;">Lake View</div>
 @endif
 <h1 style="font-size: 22px; font-weight: bold; margin: 3px 0; letter-spacing: 1px;">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</h1>
 <p style="font-size: 11px; margin: 1px 0; font-style: italic;">It's Institution of Tufan Company Limited</p>
 <p style="font-size: 11px; margin: 1px 0;">{{ $resortInfo->address ?? 'Kamalnagor, Satkhira' }}</p>
 <p style="font-size: 11px; margin: 1px 0;">E-mail: {{ $resortInfo->email ?? 'tufanresort@gmail.com' }}, Mob. {{ $resortInfo->phone ?? '01958 216728' }}</p>
 </div>

 <!-- Title -->
 <div style="text-align: center; margin: 12px 0 10px;">
 <h2 style="font-size: 18px; font-weight: bold; text-decoration: underline; letter-spacing: 2px; margin: 0;">RESERVATION LETTER</h2>
 </div>

 <!-- Date and Booking No -->
 <table style="width: 100%; margin-bottom: 10px; font-size: 12px;">
 <tr>
 <td style="width: 50%;"><strong>Date:</strong> {{ now()->format('d/m/Y') }}</td>
 <td style="width: 50%; text-align: right;"><strong>Booking No:</strong> #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</td>
 </tr>
 </table>

 <!-- Guest Information -->
 <div style="border: 1px solid #333; padding: 8px; margin-bottom: 10px; font-size: 12px;">
 <p style="margin: 0 0 5px 0; font-weight: bold; text-decoration: underline;">Guest Information:</p>
 <table style="width: 100%;">
 <tr>
 <td style="width: 50%; padding: 2px 0;"><strong>Name:</strong> {{ $booking->customer_name }}</td>
 <td style="width: 50%; padding: 2px 0;"><strong>Phone:</strong> {{ $booking->customer_phone ?? 'N/A' }}</td>
 </tr>
 <tr>
 <td style="padding: 2px 0;"><strong>Address:</strong> {{ $booking->customer_address ?? 'N/A' }}</td>
 <td style="padding: 2px 0;"><strong>NID:</strong> {{ $booking->customer_nid ?? 'N/A' }}</td>
 </tr>
 @if($booking->company_name)
 <tr>
 <td style="padding: 2px 0;"><strong>Company:</strong> {{ $booking->company_name }}</td>
 <td style="padding: 2px 0;"><strong>Guests:</strong> {{ $booking->number_of_guests }} Person(s)</td>
 </tr>
 @else
 <tr>
 <td colspan="2" style="padding: 2px 0;"><strong>Number of Guests:</strong> {{ $booking->number_of_guests }} Person(s)</td>
 </tr>
 @endif
 </table>
 </div>

 <!-- Additional Guests if any -->
 @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
 <div style="margin-bottom: 8px; padding: 5px; border: 1px solid #ddd; font-size: 11px;">
 <strong>Additional Guests ({{ $booking->additionalGuests->count() }}):</strong>
 @foreach($booking->additionalGuests as $guest)
 {{ $guest->name }}{{ $guest->nid ? ' (' . $guest->nid . ')' : '' }}{{ !$loop->last ? ', ' : '' }}
 @endforeach
 </div>
 @endif

 <!-- Kind Attention Message -->
 <div style="margin-bottom: 10px; font-size: 12px;">
 <p style="margin: 2px 0;">Thank you for choosing {{ $resortInfo->resort_name ?? 'Tufan Resort' }}. Please find the reservation details mentioned below:</p>
 </div>

 <!-- Reservation Details Table -->
 @php
 $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
 $nights = max(1, $nights);
 $allRooms = $booking->getAllRooms();
 $bookingRooms = $booking->bookingRooms;
 
 // Calculate totals from actual rooms
 $baseAmount = $booking->getCalculatedTotal();
 $discountAmount = 0;
 
 if($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
 $discountAmount = ($baseAmount * $booking->discount_percentage) / 100;
 } elseif($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
 $discountAmount = $booking->discount_amount;
 }
 
 $afterDiscount = $baseAmount - $discountAmount;
 $extraCharges = $booking->extra_charges ?? 0;
 $vatAmount = $booking->vat_enabled ? ($afterDiscount * 0.15) : 0;
 $grandTotal = $afterDiscount + $extraCharges + $vatAmount;
 $totalDeposited = $booking->getTotalDeposited();
$remainingPayment = max(0, $grandTotal - $totalDeposited);
 
 // Convert to words
 $amountInWords = \App\Helpers\NumberToWords::convertTaka($grandTotal);
 @endphp

 <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 11px;">
 <thead>
 <tr style="background-color: #f3f3f3;">
 <th style="border: 1px solid #333; padding: 5px; text-align: center;">Check-in</th>
 <th style="border: 1px solid #333; padding: 5px; text-align: center;">Check-out</th>
 <th style="border: 1px solid #333; padding: 5px; text-align: center;">Room No</th>
 <th style="border: 1px solid #333; padding: 5px; text-align: center;">Room Type</th>
 <th style="border: 1px solid #333; padding: 5px; text-align: center;">Nights</th>
 <th style="border: 1px solid #333; padding: 5px; text-align: right;">Rate/Night</th>
 <th style="border: 1px solid #333; padding: 5px; text-align: right;">Amount</th>
 </tr>
 </thead>
 <tbody>
 @if($allRooms->count() > 0)
 @foreach($allRooms as $index => $room)
 @php
 $bookingRoom = $bookingRooms->where('room_id', $room->id)->first();
 $roomPrice = $bookingRoom ? $bookingRoom->price_per_night : ($room->roomType->price_per_night ?? $room->price_per_night ?? 0);
 $roomAmount = $nights * $roomPrice;
 @endphp
 <tr>
 @if($index === 0)
 <td style="border: 1px solid #333; padding: 5px; text-align: center;" rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;" rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
 @endif
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">{{ $room->room_number }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">{{ $room->roomType->name ?? 'Room' }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">{{ $nights }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: right;">{{ number_format($roomPrice, 0) }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: right;">{{ number_format($roomAmount, 0) }}</td>
 </tr>
 @endforeach
 @else
 <tr>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">-</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">-</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: center;">{{ $nights }}</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: right;">-</td>
 <td style="border: 1px solid #333; padding: 5px; text-align: right;">{{ number_format($baseAmount, 0) }}</td>
 </tr>
 @endif
 </tbody>
 </table>

 <!-- Payment Summary -->
 <table style="width: 100%; margin-bottom: 10px; font-size: 11px;">
 <tr>
 <td style="width: 60%; vertical-align: top;">
 <p style="margin: 2px 0;"><strong>In Word:</strong> {{ $amountInWords }}</p>
 </td>
 <td style="width: 40%; vertical-align: top;">
 <table style="width: 100%; font-size: 11px;">
 <tr>
 <td style="padding: 2px 5px; text-align: right;">Room Total:</td>
 <td style="padding: 2px 5px; text-align: right; min-width: 70px;">{{ number_format($baseAmount, 0) }}</td>
 </tr>
 @if($discountAmount > 0)
 <tr style="color: #c00;">
 <td style="padding: 2px 5px; text-align: right;">Discount @if($booking->discount_type === 'percentage')({{ $booking->discount_percentage }}%)@endif:</td>
 <td style="padding: 2px 5px; text-align: right;">-{{ number_format($discountAmount, 0) }}</td>
 </tr>
 @endif
 @if($extraCharges > 0)
 <tr>
 <td style="padding: 2px 5px; text-align: right;">Extra Charges:</td>
 <td style="padding: 2px 5px; text-align: right;">{{ number_format($extraCharges, 0) }}</td>
 </tr>
 @endif
 @if($vatAmount > 0)
 <tr>
 <td style="padding: 2px 5px; text-align: right;">VAT (15%):</td>
 <td style="padding: 2px 5px; text-align: right;">{{ number_format($vatAmount, 0) }}</td>
 </tr>
 @endif
 <tr style="font-weight: bold;">
 <td style="padding: 2px 5px; text-align: right; border-top: 1px solid #000;">Grand Total:</td>
 <td style="padding: 2px 5px; text-align: right; border-top: 1px solid #000;">{{ number_format($grandTotal, 0) }}</td>
 </tr>
 <tr style="color: #060;">
 <td style="padding: 2px 5px; text-align: right;">Advance Paid:</td>
 <td style="padding: 2px 5px; text-align: right;">{{ number_format($booking->advance_payment, 0) }}</td>
 </tr>
 <tr style="font-weight: bold; color: #c00;">
 <td style="padding: 2px 5px; text-align: right;">Due Amount:</td>
 <td style="padding: 2px 5px; text-align: right;">{{ number_format($remainingPayment, 0) }}</td>
 </tr>
 </table>
 </td>
 </tr>
 </table>

 <!-- Amenities Section -->
 <div style="margin-bottom: 10px; font-size: 11px; line-height: 1.3;">
 <p style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">The following amenities are included free of cost:</p>
 <table style="width: 100%;">
 <tr>
 <td style="width: 50%; vertical-align: top; padding: 1px 0;">
 <span style="display: block; margin: 1px 0;">&#8226; Wi-Fi Facility</span>
 <span style="display: block; margin: 1px 0;">&#8226; Daily One Mineral Water Bottle (1L)</span>
 <span style="display: block; margin: 1px 0;">&#8226; Safety Car Parking</span>
 <span style="display: block; margin: 1px 0;">&#8226; 24 Hour Control by CC Camera</span>
 <span style="display: block; margin: 1px 0;">&#8226; Room Service (24 Hours)</span>
 <span style="display: block; margin: 1px 0;">&#8226; LED TV</span>
 </td>
 <td style="width: 50%; vertical-align: top; padding: 1px 0;">
 <span style="display: block; margin: 1px 0;">&#8226; Morning Breakfast (Complimentary)</span>
 <span style="display: block; margin: 1px 0;">&#8226; Own Security System</span>
 <span style="display: block; margin: 1px 0;">&#8226; Modern Furniture</span>
 <span style="display: block; margin: 1px 0;">&#8226; Intercom Telephone</span>
 <span style="display: block; margin: 1px 0;">&#8226; Hot Water facility</span>
 <span style="display: block; margin: 1px 0;">&#8226; Own Restaurant</span>
 </td>
 </tr>
 </table>
 </div>

 <!-- Policy Section -->
 <div style="margin-bottom: 10px; font-size: 11px; line-height: 1.3;">
 <p style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">Our Policy:</p>
 <p style="margin: 1px 0;">Check-in time: 12:00 hours & Check-out time: 12:00 hours.</p>
 <p style="margin: 1px 0;">Cancellation policy: Cancellation and Refund policy not applicable.</p>
 <p style="margin: 1px 0;">Booking confirmation must be done 15 days before journey date.</p>
 </div>

 <!-- Thank You Message -->
 <div style="margin-top: 10px; font-size: 11px; line-height: 1.3;">
 <p style="margin: 2px 0;">We assure you of our best services and hospitality at all times. We hope you will enjoy a memorable stay with us.</p>
 <p style="margin: 2px 0;">Please feel free to contact us for any further information/queries.</p>
 </div>

 <!-- Footer Contact -->
 <div style="margin-top: 15px; font-size: 11px;">
 <p style="margin: 2px 0; font-weight: bold;">Thanking you,</p>
 <p style="margin: 2px 0; font-weight: bold;">{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</p>
 <p style="margin: 2px 0;">{{ $resortInfo->address ?? 'Kamalnagar, Satkhira' }}</p>
 <p style="margin: 2px 0;">E-mail: {{ $resortInfo->email ?? 'tufanresort@gmail.com' }} | Phone: {{ $resortInfo->phone ?? '01958-216728' }}</p>
 </div>

 <!-- Developer Credit -->
 <div style="text-align: center; margin-top: 15px; padding-top: 8px; border-top: 1px dashed #ccc;">
 <p style="font-size: 9px; color: #666; margin: 0;">Developed By Mir Javed Jeetu | 01811480222</p>
 </div>
 </div>
</div>
