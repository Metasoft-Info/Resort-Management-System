<!-- Print-only Invoice Template - BILL Format -->
<div id="invoice-print-area" class="hidden print:block">
 <div class="invoice-container bg-white" style="font-family: 'Times New Roman', Times, serif; font-size: 15px; padding: 8mm 12mm; width: 210mm; min-height: 287mm; margin: 0 auto; box-sizing: border-box; display: flex; flex-direction: column;">
 
 @include('components.resort-document-heading')

 <!-- BILL Title -->
 <div style="text-align: center; margin: 2px 0;">
 <h2 style="font-size: 20px; font-weight: bold; text-decoration: underline; letter-spacing: 4px; margin: 0;">BILL</h2>
 </div>

 <!-- Guest Info Row - Table Layout for Print -->
 <table style="width: 100%; margin-bottom: 4px; font-size: 15px; line-height: 1.3;">
 <tr>
 <td style="width: 60%; vertical-align: top;">
 <p style="margin: 2px 0;"><strong>Name:</strong> {{ $booking->customer_name }}</p>
 <p style="margin: 2px 0;"><strong>Address:</strong> {{ $booking->customer_address ?? 'N/A' }}</p>
 @if($booking->customer_phone)
 <p style="margin: 2px 0;"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
 @endif
 @if($booking->company_name)
 <p style="margin: 2px 0;"><strong>Company:</strong> {{ $booking->company_name }}</p>
 @endif
 @if($booking->customer_nid)
 <p style="margin: 2px 0;"><strong>NID:</strong> {{ $booking->customer_nid }}</p>
 @endif
 </td>
 <td style="width: 40%; vertical-align: top; text-align: right;">
 <p style="margin: 2px 0;"><strong>Date:</strong> {{ now()->format('d/m/Y') }}</p>
 <p style="margin: 2px 0;"><strong>Bill No:</strong> #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
 <p style="margin: 2px 0;"><strong>Guests:</strong> {{ $booking->number_of_guests }} Person(s)</p>
 </td>
 </tr>
 </table>

 <!-- Additional Guests if any -->
 @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
 <div style="margin-bottom: 6px; padding: 5px; border: 1px solid #ddd; font-size: 14px;">
 <strong>Additional Guests ({{ $booking->additionalGuests->count() }}):</strong>
 @foreach($booking->additionalGuests as $guest)
 {{ $guest->name }}{{ $guest->nid ? ' (' . $guest->nid . ')' : '' }}{{ !$loop->last ? ', ' : '' }}
 @endforeach
 </div>
 @endif

 <!-- Billing Table -->
 @php
 $invoiceNights = $booking->getNights();
 $allRooms = $booking->getAllRooms();
 $bookingRooms = $booking->bookingRooms;
 $roomBreakdown = $booking->getRoomBreakdown();
 
 // Use model method for consistent base amount calculation
 $invoiceBaseAmount = $booking->getCalculatedTotal();
 
 $invoiceDiscountAmount = $booking->getDiscountAmount();
 $invoiceAfterDiscount = max(0, $invoiceBaseAmount - $invoiceDiscountAmount);
 $invoiceExtraCharges = max(0, (float) ($booking->extra_charges ?? 0));
 $invoiceVatAmount = $booking->getVatAmount();
 $invoiceGrandTotal = $booking->getGrandTotal();
 $invoiceTotalDeposited = $booking->getTotalDeposited();
$invoiceRemainingPayment = max(0, $invoiceGrandTotal - $invoiceTotalDeposited);
 
 // Convert to words
 $amountInWords = \App\Helpers\NumberToWords::convertTaka($invoiceGrandTotal);
 @endphp

 <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; font-size: 14px;">
 <thead>
 <tr style="background-color: #f5f5f5;">
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Arrival Date</th>
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Departure Date</th>
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Room No.</th>
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Room Name</th>
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Stay</th>
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Room Rent</th>
 <th style="border: 1px solid #000; padding: 4px; text-align: center; font-weight: bold;">Amount</th>
 </tr>
 </thead>
 <tbody>
 @if($roomBreakdown->isNotEmpty())
 @foreach($roomBreakdown as $roomLine)
 <tr>
 <td style="border:1px solid #000;padding:4px;text-align:center;">{{ \Carbon\Carbon::parse($roomLine['check_in_date'])->format('d/m/Y') }}</td>
 <td style="border:1px solid #000;padding:4px;text-align:center;">{{ \Carbon\Carbon::parse($roomLine['check_out_date'])->format('d/m/Y') }}</td>
 <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $roomLine['room']?->room_number ?? $roomLine['room_number'] ?? '—' }}{{ !empty($roomLine['shifted']) ? ' (shifted)' : '' }}</td>
 <td style="border:1px solid #000;padding:4px;">{{ $roomLine['room']?->roomType?->name ?? 'Room' }}</td>
 <td style="border:1px solid #000;padding:4px;text-align:center;">{{ $roomLine['nights'] }}</td>
 <td style="border:1px solid #000;padding:4px;text-align:right;">{{ number_format($roomLine['price_per_night'], 2) }}</td>
 <td style="border:1px solid #000;padding:4px;text-align:right;">{{ number_format($roomLine['amount'], 2) }}</td>
 </tr>
 @endforeach
 @else
 <tr>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">-</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">-</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $invoiceNights }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">-</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($invoiceBaseAmount, 0) }}/-</td>
 </tr>
 @endif
 </tbody>
 </table>

 <!-- Detailed Transaction Summary -->
 <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; font-size: 14px;">
 <tr>
 <td style="width: 60%; vertical-align: top; padding-right: 10px;">
 <p style="margin: 2px 0; font-size: 14px;"><strong>In Word:</strong> {{ $amountInWords }}</p>
 @if($invoiceExtraCharges > 0)
 <p style="margin: 2px 0; font-size: 14px;"><strong>Extra Charges:</strong> @if($booking->extra_charges_description){{ $booking->extra_charges_description }} - @endif{{ number_format($invoiceExtraCharges, 0) }}</p>
 @endif
 <p style="margin: 2px 0; font-size: 14px;"><strong>Payment Method:</strong> {{ $booking->is_complimentary ? 'Complimentary — no payment required' : ucfirst($booking->payment_method ?? 'Cash') }}</p>
 @if($booking->is_complimentary && $booking->complimentary_reason)
 <p style="margin: 2px 0; font-size: 14px;"><strong>Complimentary Reason:</strong> {{ $booking->complimentary_reason }}</p>
 @endif
 </td>
 <td style="width: 40%; vertical-align: top;">
 <table style="width: 100%; font-size: 14px;">
 <tr>
 <td style="padding: 2px 4px; text-align: right;"><strong>Room Total:</strong></td>
 <td style="padding: 2px 4px; text-align: right; min-width: 75px;">{{ number_format($invoiceBaseAmount, 0) }}/-</td>
 </tr>
 @if($booking->is_complimentary)
 <tr><td style="padding:2px 4px;text-align:right;">Complimentary:</td><td style="padding:2px 4px;text-align:right;">-{{ number_format($booking->getComplimentaryAmount(), 2) }}</td></tr>
 @endif
 @if($invoiceDiscountAmount > 0)
 <tr style="color: #c00;">
 <td style="padding: 2px 4px; text-align: right;">Discount @if($booking->discount_type === 'percentage')({{ $booking->discount_percentage }}%)@endif:</td>
 <td style="padding: 2px 4px; text-align: right;">-{{ number_format($invoiceDiscountAmount, 0) }}/-</td>
 </tr>
 @endif
 @if($invoiceExtraCharges > 0)
 <tr>
 <td style="padding: 2px 4px; text-align: right;">Extra Charges:</td>
 <td style="padding: 2px 4px; text-align: right;">{{ number_format($invoiceExtraCharges, 0) }}/-</td>
 </tr>
 @endif
 @if($invoiceVatAmount > 0)
 <tr>
 <td style="padding: 2px 4px; text-align: right;">VAT (15%):</td>
 <td style="padding: 2px 4px; text-align: right;">{{ number_format($invoiceVatAmount, 0) }}/-</td>
 </tr>
 @endif
 <tr style="font-weight: bold; font-size: 15px;">
 <td style="padding: 3px 4px; text-align: right; border-top: 1px solid #000; border-bottom: 2px solid #000;"><strong>Grand Total:</strong></td>
 <td style="padding: 3px 4px; text-align: right; border-top: 1px solid #000; border-bottom: 2px solid #000;">{{ number_format($invoiceGrandTotal, 0) }}/-</td>
 </tr>
 <tr style="color: #060;">
 <td style="padding: 2px 4px; text-align: right;">Total Paid:</td>
 <td style="padding: 2px 4px; text-align: right;">{{ number_format($invoiceTotalDeposited, 0) }}/-</td>
 </tr>
 <tr style="font-weight: bold; color: #c00;">
 <td style="padding: 2px 4px; text-align: right;"><strong>Due Amount:</strong></td>
 <td style="padding: 2px 4px; text-align: right;">{{ number_format($invoiceRemainingPayment, 0) }}/-</td>
 </tr>
 </table>
 </td>
 </tr>
 </table>

 <!-- Payment History if exists -->
 @if($booking->payments && $booking->payments->count() > 0)
 <div style="margin-bottom: 6px; font-size: 12px;">
 <strong>Payment History:</strong>
 <table style="width: 100%; border-collapse: collapse; margin-top: 3px;">
 <tr style="background-color: #f5f5f5;">
 <th style="border: 1px solid #ccc; padding: 3px; text-align: left;">Date</th>
 <th style="border: 1px solid #ccc; padding: 3px; text-align: left;">Method</th>
 <th style="border: 1px solid #ccc; padding: 3px; text-align: right;">Amount</th>
 <th style="border: 1px solid #ccc; padding: 3px; text-align: left;">Note</th>
 </tr>
 @foreach($booking->payments as $payment)
 <tr>
 <td style="border: 1px solid #ccc; padding: 3px;">{{ $payment->created_at->format('d/m/Y') }}</td>
 <td style="border: 1px solid #ccc; padding: 3px;">{{ ucfirst($payment->method) }}</td>
 <td style="border: 1px solid #ccc; padding: 3px; text-align: right;">{{ number_format($payment->amount, 0) }}</td>
 <td style="border: 1px solid #ccc; padding: 3px;">{{ $payment->note ?? '-' }}</td>
 </tr>
 @endforeach
 </table>
 </div>
 @endif

 <!-- Signature Section -->
 <div style="display: flex; justify-content: space-between; margin: 12px 0 8px;">
 <div style="text-align: center; width: 45%;">
 <div style="border-top: 1px solid #000; padding-top: 4px; margin-top: 24px;">
 <span style="font-size: 13px;">Guest Signature</span>
 </div>
 </div>
 <div style="text-align: center; width: 45%;">
 <div style="border-top: 1px solid #000; padding-top: 4px; margin-top: 24px;">
 <span style="font-size: 13px;">Authorised Signature</span>
 </div>
 </div>
 </div>

 <!-- Amenities Section -->
 <div style="margin-bottom: 4px; font-size: 12px; line-height: 1.3;">
 <p style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">The following amenities are included free of cost, as part of your stay:</p>
 <table style="width: 100%;">
 <tr>
 <td style="width: 50%; vertical-align: top; padding: 0;">
 <span style="display: block; margin: 1px 0;">&#8226; Wi-Fi Facility</span>
 <span style="display: block; margin: 1px 0;">&#8226; Daily One Mineral Water Bottle Complimentary (1L)</span>
 <span style="display: block; margin: 1px 0;">&#8226; Safety Car Parking</span>
 <span style="display: block; margin: 1px 0;">&#8226; 24 Houry Control by CC Camera</span>
 <span style="display: block; margin: 1px 0;">&#8226; Room Service (24 Hours)</span>
 <span style="display: block; margin: 1px 0;">&#8226; LED TV</span>
 <span style="display: block; margin: 1px 0;">&#8226; Own Restaurant</span>
 </td>
 <td style="width: 50%; vertical-align: top; padding: 0;">
 <span style="display: block; margin: 1px 0;">&#8226; Morning Breakfast (Complimentary)</span>
 <span style="display: block; margin: 1px 0;">&#8226; Own Security System</span>
 <span style="display: block; margin: 1px 0;">&#8226; Modern Furniture</span>
 <span style="display: block; margin: 1px 0;">&#8226; Intercom Telephone</span>
 <span style="display: block; margin: 1px 0;">&#8226; Hot Water facility</span>
 </td>
 </tr>
 </table>
 </div>

 <!-- Policy Section -->
 <div style="margin-bottom: 4px; font-size: 12px; line-height: 1.3;">
 <p style="font-weight: bold; text-decoration: underline; margin-bottom: 3px;">Our Policy:</p>
 <p style="margin: 1px 0;">Our check-in time 12:00 hours & check-out 12:00 hours.</p>
 <p style="margin: 1px 0;">Cancellation policy: Cancellation and Refund policy not applicable.</p>
 <p style="margin: 1px 0;">Booking confirmation us be done 15 days beore journey date.</p>
 </div>

 <!-- Thank You Message -->
 <div style="margin-top: 4px; font-size: 13px; line-height: 1.3;">
 <p style="margin: 2px 0;">We assure you of our best services and hospitality at all times. We hope you will enjoy memorable stay with us.</p>
 <p style="margin: 2px 0;">Please feel free to contact us for any further information/queries.</p>
 </div>

 <!-- Developer Credit -->
 <div style="text-align: center; margin-top: 10px; padding-top: 8px; border-top: 1px dashed #ccc; flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-end;">
 <p style="font-size: 10px; color: #666; margin: 0 0 5px;">Developed By Mir Javed Jeetu | 01811480222</p>
 <p style="font-size: 15px; font-weight: bold; color: #333; margin: 0;">Thank you for choosing Tufan Resort</p>
 <p style="font-size: 14px; color: #555; margin: 3px 0 0;">For reservations call: {{ $resortInfo->phone ?? '01958 216728' }}</p>
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
 -webkit-print-color-adjust: exact !important;
 print-color-adjust: exact !important;
 margin: 0;
 padding: 0;
 height: 297mm !important;
 overflow: visible !important;
 }
 
 body * {
 visibility: hidden;
 }
 
 body.print-invoice #invoice-print-area,
 body.print-invoice #invoice-print-area * {
 visibility: visible !important;
 }
 
 body.print-invoice #reservation-print-area {
 display: none !important;
 }
 
 body.print-reservation #reservation-print-area,
 body.print-reservation #reservation-print-area * {
 visibility: visible !important;
 }
 
 body.print-reservation #invoice-print-area {
 display: none !important;
 }
 
 #invoice-print-area, #reservation-print-area {
 position: absolute;
 left: 0;
 top: 0;
 width: 100%;
 height: 287mm !important;
 overflow: visible !important;
 }
 
 .invoice-container, .reservation-container {
 width: 100%;
 height: 287mm !important;
 overflow: visible !important;
 }
 
 .print\:block {
 display: block !important;
 }
 
 .print\:hidden {
 display: none !important;
 }
}
</style>
