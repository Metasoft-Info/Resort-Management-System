<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservation Letter</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            padding: 15mm;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header img {
            height: 60px;
        }
        .header h1 {
            font-size: 22px;
            margin: 5px 0;
        }
        .header p {
            font-size: 11px;
            margin: 2px 0;
        }
        .title {
            text-align: center;
            margin: 15px 0;
        }
        .title h2 {
            font-size: 18px;
            text-decoration: underline;
            letter-spacing: 2px;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-row table {
            width: 100%;
        }
        .info-row td {
            vertical-align: top;
            padding: 2px 0;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        .main-table th {
            background-color: #f5f5f5;
        }
        .amenities {
            margin-bottom: 12px;
        }
        .amenities-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .amenities table {
            width: 100%;
        }
        .amenities td {
            padding: 1px 0;
            font-size: 11px;
        }
        .policy {
            margin-bottom: 12px;
            font-size: 11px;
        }
        .policy-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .thanks {
            font-size: 11px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            font-size: 9px;
            color: #666;
        }
    </style>
</head>
<body>
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
        <h2>RESERVATION LETTER</h2>
    </div>

    <!-- Guest Info -->
    <div class="info-row">
        <table>
            <tr>
                <td style="width: 60%;">
                    <p><strong>Name:</strong> {{ $booking->customer_name }}</p>
                    <p><strong>Address:</strong> {{ $booking->customer_address ?? 'N/A' }}</p>
                    @if($booking->customer_phone)
                    <p><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
                    @endif
                </td>
                <td style="text-align: right;">
                    <p><strong>Date:</strong> {{ now()->format('d/m/Y') }}</p>
                    <p><strong>Booking No:</strong> #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                </td>
            </tr>
        </table>
    </div>

    @php
        $nights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
        $nights = max(1, $nights);
        $allRooms = $booking->getAllRooms();
    @endphp

    <!-- Booking Details Table -->
    <table class="main-table">
        <thead>
            <tr>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Room(s)</th>
                <th>Guests</th>
                <th>Nights</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M, Y') }}</td>
                <td>
                    @if($allRooms->count() > 0)
                        @foreach($allRooms as $room)
                            {{ $room->room_number }}@if(!$loop->last), @endif
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>{{ $booking->number_of_guests }}</td>
                <td>{{ $nights }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Summary -->
    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="text-align: right;">
                <strong>Total Amount:</strong> BDT {{ number_format($booking->getCalculatedTotal(), 0) }} | 
                <strong>Total Paid:</strong> <span style="color: green;">BDT {{ number_format($booking->getTotalDeposited(), 0) }}</span> | 
                <strong>Due:</strong> <span style="color: red;">BDT {{ number_format(max(0, $booking->getCalculatedRemaining()), 0) }}</span>
            </td>
        </tr>
    </table>

    <!-- Amenities -->
    <div class="amenities">
        <p class="amenities-title">The following amenities are included free of cost, as part of your stay:</p>
        <table>
            <tr>
                <td style="width: 50%;">• Wi-Fi Facility</td>
                <td>• Morning Breakfast (Complimentary)</td>
            </tr>
            <tr>
                <td>• Daily One Mineral Water Bottle (1L)</td>
                <td>• Own Security System</td>
            </tr>
            <tr>
                <td>• Safety Car Parking</td>
                <td>• Modern Furniture</td>
            </tr>
            <tr>
                <td>• 24 Hours CC Camera</td>
                <td>• Intercom Telephone</td>
            </tr>
            <tr>
                <td>• Room Service (24 Hours)</td>
                <td>• Hot Water facility</td>
            </tr>
            <tr>
                <td>• LED TV</td>
                <td></td>
            </tr>
            <tr>
                <td>• Own Restaurant</td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Policy -->
    <div class="policy">
        <p class="policy-title">Our Policy:</p>
        <p>Our check-in time 12:00 hours & check-out 12:00 hours.</p>
        <p>Cancellation policy: Cancellation and Refund policy not applicable.</p>
        <p>Booking confirmation us be done 15 days before journey date.</p>
    </div>

    <!-- Thanks -->
    <div class="thanks">
        <p>We assure you of our best services and hospitality at all times. We hope you will enjoy memorable stay with us.</p>
        <p>Please feel free to contact us for any further information/queries.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        Developed By Mir Javed Jeetu | 01811480222
    </div>
</body>
</html>
