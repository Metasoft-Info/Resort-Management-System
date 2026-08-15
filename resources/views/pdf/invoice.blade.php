<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 10mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header img {
            height: 55px;
        }
        .header h1 {
            font-size: 20px;
            margin: 3px 0;
        }
        .header p {
            font-size: 10px;
            margin: 1px 0;
        }
        .title {
            text-align: center;
            margin: 10px 0;
        }
        .title h2 {
            font-size: 18px;
            text-decoration: underline;
            letter-spacing: 3px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-row table {
            width: 100%;
        }
        .info-row td {
            vertical-align: top;
            font-size: 11px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 11px;
        }
        .main-table th {
            background-color: #f5f5f5;
        }
        .totals-table {
            width: 100%;
            margin-bottom: 8px;
        }
        .totals-table td {
            padding: 2px 5px;
            font-size: 11px;
        }
        .signatures {
            margin: 20px 0 10px;
        }
        .signatures table {
            width: 100%;
        }
        .signatures td {
            width: 45%;
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #000;
            font-size: 10px;
        }
        .amenities {
            margin-bottom: 8px;
            font-size: 10px;
        }
        .amenities-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .amenities table td {
            padding: 1px 0;
        }
        .policy {
            margin-bottom: 8px;
            font-size: 10px;
        }
        .policy-title {
            font-weight: bold;
            text-decoration: underline;
        }
        .thanks {
            font-size: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
            font-size: 8px;
            color: #666;
        }
        .payment-history {
            margin-bottom: 8px;
            font-size: 10px;
        }
        .payment-history th, .payment-history td {
            border: 1px solid #ccc;
            padding: 3px;
        }
    </style>
</head>
<body>
    @php
        $invoiceNights = $booking->getNights();
        $allRooms = $booking->getAllRooms();
        $bookingRooms = $booking->bookingRooms;
        $roomBreakdown = $booking->getRoomBreakdown();
        $invoiceBaseAmount = $booking->getCalculatedTotal();
        $invoiceDiscountAmount = $booking->getDiscountAmount();
        $invoiceExtraCharges = $booking->extra_charges ?? 0;
        $invoiceVatAmount = $booking->getVatAmount();
        $invoiceGrandTotal = $booking->getGrandTotal();
        $amountInWords = \App\Helpers\NumberToWords::convertTaka($invoiceGrandTotal);
    @endphp

    <!-- Header -->
    <div class="header">
        @if($resortInfo && $resortInfo->header_logo)
            <img src="{{ public_path('storage/' . $resortInfo->header_logo) }}" alt="Logo">
        @endif
        <h1>{{ $resortInfo->resort_name ?? 'Tufan Resort' }}</h1>
        <p style="font-style: italic;">It's Institution of Tufan Company Limited</p>
        <p>{{ $resortInfo->address ?? 'Kamalnagor, Satkhira' }}</p>
        <p>E-mail: {{ $resortInfo->email ?? 'tufanresort@gmail.com' }}, Mob. {{ $resortInfo->phone ?? '01958 216728' }}</p>
    </div>

    <!-- Title -->
    <div class="title">
        <h2>BILL</h2>
    </div>

    <!-- Guest Info -->
    <div class="info-row">
        <table>
            <tr>
                <td style="width: 60%;">
                    <p style="margin: 2px 0;"><strong>Name:</strong> {{ $booking->customer_name }}</p>
                    <p style="margin: 2px 0;"><strong>Address:</strong> {{ $booking->customer_address ?? 'N/A' }}</p>
                    @if($booking->customer_phone)
                    <p style="margin: 2px 0;"><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                    @endif
                    @if($booking->company_name)
                    <p style="margin: 2px 0;"><strong>Company:</strong> {{ $booking->company_name }}</p>
                    @endif
                </td>
                <td style="text-align: right;">
                    <p style="margin: 2px 0;"><strong>Date:</strong> {{ now()->format('d/m/Y') }}</p>
                    <p style="margin: 2px 0;"><strong>Bill No:</strong> #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                    @if($booking->customer_nid)
                    <p style="margin: 2px 0;"><strong>NID:</strong> {{ $booking->customer_nid }}</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Additional Guests -->
    @if($booking->additionalGuests && $booking->additionalGuests->count() > 0)
    <div style="margin-bottom: 5px; padding: 3px; border: 1px solid #ddd; font-size: 10px;">
        <strong>Additional Guests ({{ $booking->additionalGuests->count() }}):</strong>
        @foreach($booking->additionalGuests as $guest)
            {{ $guest->name }}@if($guest->nid) ({{ $guest->nid }})@endif@if(!$loop->last), @endif
        @endforeach
    </div>
    @endif

    <!-- Room Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th>Arrival Date</th>
                <th>Departure Date</th>
                <th>Room Number</th>
                <th>Room Name</th>
                <th>Stay</th>
                <th>Room Rent</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($allRooms->count() > 0)
                @foreach($allRooms as $index => $room)
                    @php
                        $roomLine = $roomBreakdown->firstWhere('room_id', $room->id);
                        $roomPricePerNight = $roomLine['price_per_night'] ?? 0;
                        $roomNights = $roomLine['nights'] ?? $invoiceNights;
                        $roomAmount = $roomLine['amount'] ?? ($roomNights * $roomPricePerNight);
                    @endphp
                    <tr>
                        @if($index === 0)
                        <td rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                        <td rowspan="{{ $allRooms->count() }}">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                        @endif
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->roomType->name ?? 'Room' }}</td>
                        <td>{{ $roomNights }}</td>
                        <td style="text-align: right;">{{ number_format($roomPricePerNight, 0) }}/-</td>
                        <td style="text-align: right;">{{ number_format($roomAmount, 0) }}/-</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>{{ $invoiceNights }}</td>
                    <td style="text-align: right;">-</td>
                    <td style="text-align: right;">{{ number_format($invoiceBaseAmount, 0) }}/-</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <p style="margin: 2px 0;"><strong>In Word:</strong> {{ $amountInWords }}</p>
                @if($invoiceExtraCharges > 0)
                <p style="margin: 2px 0;"><strong>Extra:</strong> {{ $booking->extra_charges_description ?? '' }} - BDT {{ number_format($invoiceExtraCharges, 0) }}</p>
                @endif
                <p style="margin: 2px 0;"><strong>Payment:</strong> {{ ucfirst($booking->payment_method ?? 'Cash') }}</p>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right;"><strong>Room Total:</strong></td>
                        <td style="text-align: right; width: 80px;">{{ number_format($invoiceBaseAmount, 0) }}/-</td>
                    </tr>
                    @if($invoiceExtraCharges > 0)
                    <tr>
                        <td style="text-align: right;">Extra:</td>
                        <td style="text-align: right;">{{ number_format($invoiceExtraCharges, 0) }}/-</td>
                    </tr>
                    @endif
                    @if($invoiceVatAmount > 0)
                    <tr>
                        <td style="text-align: right;">VAT (15%):</td>
                        <td style="text-align: right;">{{ number_format($invoiceVatAmount, 0) }}/-</td>
                    </tr>
                    @endif
                    @if($invoiceDiscountAmount > 0)
                    <tr style="color: #c00;">
                        <td style="text-align: right;">Discount:</td>
                        <td style="text-align: right;">-{{ number_format($invoiceDiscountAmount, 0) }}/-</td>
                    </tr>
                    @endif
                    <tr style="font-weight: bold; border-top: 1px solid #000;">
                        <td style="text-align: right; padding-top: 3px;"><strong>Grand Total:</strong></td>
                        <td style="text-align: right; padding-top: 3px;">{{ number_format($invoiceGrandTotal, 0) }}/-</td>
                    </tr>
                    <tr style="color: #060;">
                        <td style="text-align: right;">Total Paid:</td>
                        <td style="text-align: right;">{{ number_format($booking->getTotalDeposited(), 0) }}/-</td>
                    </tr>
                    <tr style="font-weight: bold; color: #c00;">
                        <td style="text-align: right;"><strong>Due:</strong></td>
                        <td style="text-align: right;">{{ number_format(max(0, $booking->getCalculatedRemaining()), 0) }}/-</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Payment History -->
    @if($booking->payments && $booking->payments->count() > 0)
    <div class="payment-history">
        <strong>Payment History:</strong>
        <table style="width: 100%; border-collapse: collapse; margin-top: 3px;">
            <tr style="background-color: #f5f5f5;">
                <th style="text-align: left;">Date</th>
                <th style="text-align: left;">Method</th>
                <th style="text-align: right;">Amount</th>
                <th style="text-align: left;">Note</th>
            </tr>
            @foreach($booking->payments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('d/m/Y') }}</td>
                <td>{{ ucfirst($payment->method) }}</td>
                <td style="text-align: right;">BDT {{ number_format($payment->amount, 0) }}</td>
                <td>{{ $payment->note ?? '-' }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- Signatures -->
    <div class="signatures">
        <table>
            <tr>
                <td>Guest Signature</td>
                <td></td>
                <td>Authorised Signature</td>
            </tr>
        </table>
    </div>

    <!-- Amenities -->
    <div class="amenities">
        <p class="amenities-title">The following amenities are included free of cost:</p>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">• Wi-Fi Facility</td>
                <td>• Morning Breakfast</td>
            </tr>
            <tr>
                <td>• Mineral Water Bottle (1L)</td>
                <td>• Security System</td>
            </tr>
            <tr>
                <td>• Car Parking</td>
                <td>• Modern Furniture</td>
            </tr>
            <tr>
                <td>• 24 Hours CC Camera</td>
                <td>• Intercom Telephone</td>
            </tr>
            <tr>
                <td>• Room Service (24 Hours)</td>
                <td>• Hot Water</td>
            </tr>
            <tr>
                <td>• LED TV</td>
                <td>• Restaurant</td>
            </tr>
        </table>
    </div>

    <!-- Policy -->
    <div class="policy">
        <p class="policy-title">Our Policy:</p>
        <p style="margin: 1px 0;">Check-in/out: 12:00 hours | Cancellation and Refund not applicable | Confirm 15 days before journey.</p>
    </div>

    <!-- Thanks -->
    <div class="thanks">
        <p>We assure you of our best services. Please feel free to contact us for any queries.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Developed By Mir Javed Jeetu | 01811480222
    </div>
</body>
</html>
