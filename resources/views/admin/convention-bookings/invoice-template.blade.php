<!-- Print-only Invoice Template - Convention BILL Format - Full Page Detailed -->
<div id="convention-invoice-print-area" class="hidden print:block">
 <div class="invoice-container bg-white" style="font-family: 'Times New Roman', Times, serif; font-size: 12px; padding: 6mm 10mm; width: 210mm; min-height: 287mm; margin: 0 auto; box-sizing: border-box;">
 
 <!-- Header with Logo - Centered -->
 <div style="text-align: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 10px;">
 @if($resortInfo && $resortInfo->header_logo)
 <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" style="height: 70px; margin: 0 auto 6px; display: block;">
 @else
 <div style="width: 70px; height: 70px; border: 2px solid #000; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">TUFAN</div>
 @endif
 <h1 style="font-size: 22px; font-weight: bold; margin: 4px 0; letter-spacing: 2px; text-transform: uppercase;">Tufan Convention Center</h1>
 <p style="font-size: 11px; margin: 2px 0; font-style: italic;">It's Institution of Tufan Company Limited</p>
 <p style="font-size: 12px; margin: 2px 0;">{{ $resortInfo->address ?? 'Kamalnagar, Satkhira Sadar' }}</p>
 <p style="font-size: 12px; margin: 2px 0;">Mobile: {{ $resortInfo->phone ?? '01958216727' }} | Email: {{ $resortInfo->email ?? 'info@tufanconventionresort.com' }}</p>
 </div>

 <!-- BILL Title -->
 <div style="text-align: center; margin: 8px 0; padding: 6px; background: linear-gradient(to right, #f0f0f0, #e0e0e0, #f0f0f0);">
 <h2 style="font-size: 18px; font-weight: bold; letter-spacing: 6px; margin: 0; text-transform: uppercase;">CONVENTION BILL</h2>
 </div>

 <!-- Customer & Event Info Row -->
 <table style="width: 100%; margin-bottom: 12px; font-size: 13px; line-height: 1.5;">
 <tr>
 <td style="width: 50%; vertical-align: top; padding-right: 15px;">
 <p style="margin: 3px 0;"><strong>Customer Name:</strong> {{ $booking->customer_name }}</p>
 @if($booking->organization_name)
 <p style="margin: 3px 0;"><strong>Organization:</strong> {{ $booking->organization_name }}</p>
 @endif
 <p style="margin: 3px 0;"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
 @if($booking->customer_whatsapp)
 <p style="margin: 3px 0;"><strong>WhatsApp:</strong> {{ $booking->customer_whatsapp }}</p>
 @endif
 @if($booking->customer_email)
 <p style="margin: 3px 0;"><strong>Email:</strong> {{ $booking->customer_email }}</p>
 @endif
 @if($booking->customer_address)
 <p style="margin: 3px 0;"><strong>Address:</strong> {{ $booking->customer_address }}</p>
 @endif
 @if($booking->customer_nid)
 <p style="margin: 3px 0;"><strong>NID:</strong> {{ $booking->customer_nid }}</p>
 @endif
 </td>
 <td style="width: 50%; vertical-align: top; text-align: right; border-left: 1px dashed #ccc; padding-left: 15px;">
 <p style="margin: 3px 0;"><strong>Bill Date:</strong> {{ now()->format('d/m/Y') }}</p>
 <p style="margin: 3px 0;"><strong>Bill No:</strong> CONV-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
 <p style="margin: 3px 0;"><strong>Hall:</strong> {{ $booking->conventionHall->name }}</p>
 <p style="margin: 3px 0;"><strong>Event Date:</strong> {{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</p>
 <p style="margin: 3px 0;"><strong>Time Slot:</strong> 
 @if($booking->time_slot == 'morning')
 Morning (8AM-2PM)
 @elseif($booking->time_slot == 'night')
 Night (6PM-11PM)
 @else
 Full Day (8AM-11PM)
 @endif
 </p>
 <p style="margin: 3px 0;"><strong>No. of Guests:</strong> {{ $booking->number_of_guests }} Person(s)</p>
 <p style="margin: 3px 0;"><strong>Event Type:</strong> {{ $booking->event_type }}</p>
 </td>
 </tr>
 </table>

 @if($booking->event_description)
 <div style="margin-bottom: 10px; padding: 6px 10px; background: #f8f8f8; border: 1px solid #ddd; font-size: 12px;">
 <strong>Event Description:</strong> {{ $booking->event_description }}
 </div>
 @endif

 <!-- Billing Table -->
 @php
 $addons = is_array($booking->selected_addons) ? $booking->selected_addons : json_decode($booking->selected_addons, true);
 $quantities = is_array($booking->addon_quantities) ? $booking->addon_quantities : json_decode($booking->addon_quantities, true);
 $relatedBookingsForInvoice = $relatedBookings ?? collect();
 $invoiceTotals = $groupTotals ?? [
 'hall_rent' => $booking->hall_rent,
 'food_cost' => $booking->food_cost,
 'addons_cost' => $booking->addons_cost,
 'discount' => $booking->discount,
 'vat_amount' => $booking->vat_amount,
 'total_amount' => $booking->total_amount,
 'advance_payment' => $booking->advance_payment,
 'remaining_payment' => $booking->remaining_payment,
 ];
 @endphp

 <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 12px;">
 <thead>
 <tr style="background-color: #e8e8e8;">
 <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; width: 30px;">SL</th>
 <th style="border: 1px solid #000; padding: 6px; text-align: left; font-weight: bold;">Description</th>
 <th style="border: 1px solid #000; padding: 6px; text-align: center; font-weight: bold; width: 60px;">Qty</th>
 <th style="border: 1px solid #000; padding: 6px; text-align: right; font-weight: bold; width: 80px;">Rate (BDT)</th>
 <th style="border: 1px solid #000; padding: 6px; text-align: right; font-weight: bold; width: 90px;">Amount (BDT)</th>
 </tr>
 </thead>
 <tbody>
 @php $sl = 1; @endphp
 @php
 $allHallBookings = collect([$booking])->merge($relatedBookingsForInvoice)->sortBy('id');
 @endphp
 <!-- Hall Rent for all related bookings (each hall as separate row) -->
 @foreach($allHallBookings as $hallBooking)
 <tr>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $sl++ }}</td>
 <td style="border: 1px solid #000; padding: 5px;">Convention Hall Rent - {{ $hallBooking->conventionHall->name ?? 'N/A' }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">1</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($hallBooking->hall_rent, 0) }}/-</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($hallBooking->hall_rent, 0) }}/-</td>
 </tr>
 @endforeach

 <!-- Food Package -->
 @if($booking->foodPackage && $invoiceTotals['food_cost'] > 0)
 <tr>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $sl++ }}</td>
 <td style="border: 1px solid #000; padding: 5px;">Food Package: {{ $booking->foodPackage->name }} ({{ $booking->number_of_guests }} persons × {{ number_format($booking->foodPackage->price_per_person, 0) }})</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $booking->number_of_guests }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($booking->foodPackage->price_per_person, 0) }}/-</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($invoiceTotals['food_cost'], 0) }}/-</td>
 </tr>
 @endif

 <!-- Addon Services -->
 @if($addons && count($addons) > 0 && $invoiceTotals['addons_cost'] > 0)
 @foreach($addons as $addonId)
 @php
 $addon = \App\Models\AddonService::find($addonId);
 $qty = $quantities[$addonId] ?? 1;
 @endphp
 @if($addon)
 <tr>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $sl++ }}</td>
 <td style="border: 1px solid #000; padding: 5px;">{{ $addon->name }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $qty }}</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($addon->price, 0) }}/-</td>
 <td style="border: 1px solid #000; padding: 5px; text-align: right;">{{ number_format($addon->price * $qty, 0) }}/-</td>
 </tr>
 @endif
 @endforeach
 @endif
 </tbody>
 </table>

 <!-- Summary Section -->
 @php
 $subtotal = $invoiceTotals['hall_rent'] + $invoiceTotals['food_cost'] + $invoiceTotals['addons_cost'];
 $discountAmount = $invoiceTotals['discount'] ?? 0;
 $vatAmount = $invoiceTotals['vat_amount'] ?? 0;
 $grandTotal = $invoiceTotals['total_amount'];
 $paidAmount = $invoiceTotals['advance_payment'] ?? 0;
 $dueAmount = $invoiceTotals['remaining_payment'] ?? 0;
 @endphp

 <table style="width: 100%; margin-bottom: 12px; font-size: 12px;">
 <tr>
 <td style="width: 55%; vertical-align: top; padding-right: 15px;">
 <p style="margin: 3px 0;"><strong>Payment Method:</strong> {{ ucfirst($booking->payment_method ?? 'Cash') }}</p>
 
 @if($booking->notes)
 <p style="margin: 3px 0;"><strong>Notes:</strong> {{ $booking->notes }}</p>
 @endif
 
 <!-- Payment History -->
 @if($booking->payments && $booking->payments->count() > 0)
 <div style="margin-top: 8px;">
 <p style="font-weight: bold; margin-bottom: 4px; text-decoration: underline;">Payment History:</p>
 <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
 <tr style="background: #f5f5f5;">
 <th style="border: 1px solid #ccc; padding: 3px; text-align: left;">Date</th>
 <th style="border: 1px solid #ccc; padding: 3px; text-align: left;">Method</th>
 <th style="border: 1px solid #ccc; padding: 3px; text-align: right;">Amount</th>
 </tr>
 @foreach($booking->payments as $payment)
 <tr>
 <td style="border: 1px solid #ccc; padding: 3px;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
 <td style="border: 1px solid #ccc; padding: 3px;">{{ ucfirst($payment->payment_method) }}</td>
 <td style="border: 1px solid #ccc; padding: 3px; text-align: right;">{{ number_format($payment->amount, 0) }}</td>
 </tr>
 @endforeach
 </table>
 </div>
 @endif
 </td>
 <td style="width: 45%; vertical-align: top;">
 <table style="width: 100%; font-size: 13px; border: 1px solid #000;">
 <tr>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">Hall Rent:</td>
 <td style="padding: 4px 8px; text-align: right; width: 100px; border-bottom: 1px solid #ddd;">{{ number_format($invoiceTotals['hall_rent'], 0) }}/-</td>
 </tr>
 @if($invoiceTotals['food_cost'] > 0)
 <tr>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">Food Cost:</td>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">{{ number_format($invoiceTotals['food_cost'], 0) }}/-</td>
 </tr>
 @endif
 @if($invoiceTotals['addons_cost'] > 0)
 <tr>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">Addon Services:</td>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">{{ number_format($invoiceTotals['addons_cost'], 0) }}/-</td>
 </tr>
 @endif
 <tr style="font-weight: bold;">
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #000;">Subtotal:</td>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #000;">{{ number_format($subtotal, 0) }}/-</td>
 </tr>
 @if($discountAmount > 0)
 <tr style="color: #c00;">
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">Discount:</td>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">-{{ number_format($discountAmount, 0) }}/-</td>
 </tr>
 @endif
 @if($vatAmount > 0)
 <tr>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">VAT ({{ number_format($booking->vat_percentage ?? 15, 2) }}%):</td>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">{{ number_format($vatAmount, 0) }}/-</td>
 </tr>
 @endif
 <tr style="font-weight: bold; font-size: 14px; background: #f0f0f0;">
 <td style="padding: 6px 8px; text-align: right; border-bottom: 2px solid #000;">Grand Total:</td>
 <td style="padding: 6px 8px; text-align: right; border-bottom: 2px solid #000;">{{ number_format($grandTotal, 0) }}/-</td>
 </tr>
 <tr style="color: #060;">
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">Paid Amount:</td>
 <td style="padding: 4px 8px; text-align: right; border-bottom: 1px solid #ddd;">{{ number_format($paidAmount, 0) }}/-</td>
 </tr>
 <tr style="font-weight: bold; color: #c00; font-size: 14px;">
 <td style="padding: 4px 8px; text-align: right;">Due Amount:</td>
 <td style="padding: 4px 8px; text-align: right;">{{ number_format($dueAmount, 0) }}/-</td>
 </tr>
 </table>
 </td>
 </tr>
 </table>

 <!-- Amount in Words -->
 <div style="margin-bottom: 10px; padding: 5px 8px; background: #f8f8f8; border: 1px solid #ddd; font-size: 11px;">
 <strong>In Words:</strong> {{ \App\Helpers\NumberToWords::convert($grandTotal) }} Taka Only
 </div>

 <!-- Signature Section -->
 <table style="width: 100%; margin: 15px 0 10px;">
 <tr>
 <td style="width: 35%; text-align: center;">
 <div style="border-top: 1px solid #000; padding-top: 4px; margin-top: 25px;">
 <span style="font-size: 10px;">Customer Signature</span>
 </div>
 </td>
 <td style="width: 30%;"></td>
 <td style="width: 35%; text-align: center;">
 <div style="border-top: 1px solid #000; padding-top: 4px; margin-top: 25px;">
 <span style="font-size: 10px;">Authorised Signature</span>
 </div>
 </td>
 </tr>
 </table>

 <!-- Terms & Conditions -->
 <div style="margin: 8px 0; padding: 5px 8px; background: #f5f5f5; border: 1px solid #ddd; font-size: 9px; line-height: 1.3;">
 <strong>Terms & Conditions:</strong> 1. Advance payment is non-refundable. 2. Full payment must be made before event. 3. Any damage to property will be charged separately.
 </div>

 <!-- Footer -->
 <div style="text-align: center; padding: 8px 0; border-top: 2px solid #000; margin-top: 5px; background: #f8f8f8;">
 <p style="font-size: 14px; font-weight: bold; color: #000; margin: 0 0 4px;">Thank you for choosing Tufan Convention Center</p>
 <p style="font-size: 11px; color: #333; margin: 2px 0;">For booking call: {{ $resortInfo->phone ?? '01958216727' }}</p>
 <p style="font-size: 9px; color: #666; margin: 4px 0 0; border-top: 1px dashed #ccc; padding-top: 4px;">Developed By Mir Javed Jeetu | 01811480222</p>
 </div>
 </div>
</div>

<style>
@media print {
 @page {
 size: A4;
 margin: 0;
 }
 
 html, body {
 -webkit-print-color-adjust: exact !important;
 print-color-adjust: exact !important;
 margin: 0;
 padding: 0;
 width: 210mm;
 height: 297mm;
 }
 
 body.print-convention-invoice * {
 visibility: hidden;
 }
 
 body.print-convention-invoice #convention-invoice-print-area,
 body.print-convention-invoice #convention-invoice-print-area * {
 visibility: visible !important;
 }
 
 body.print-convention-invoice #convention-invoice-print-area table,
 body.print-convention-invoice #convention-invoice-print-area tr,
 body.print-convention-invoice #convention-invoice-print-area td,
 body.print-convention-invoice #convention-invoice-print-area th,
 body.print-convention-invoice #convention-invoice-print-area thead,
 body.print-convention-invoice #convention-invoice-print-area tbody {
 display: revert !important;
 visibility: visible !important;
 }
 
 #convention-invoice-print-area {
 position: absolute;
 left: 50% !important;
 top: 0;
 transform: translateX(-50%) !important;
 width: 210mm;
 z-index: 99999 !important;
 background: white !important;
 }
 
 .invoice-container {
 width: 210mm;
 }
 
 .hidden {
 display: block !important;
 }
}
</style>
