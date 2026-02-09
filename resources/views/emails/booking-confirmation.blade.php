<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #166534; padding: 25px; text-align: center;">
                            @if($resortInfo && $resortInfo->header_logo)
                                <img src="{{ asset('storage/' . $resortInfo->header_logo) }}" alt="{{ $resortInfo->resort_name ?? 'Resort' }}" style="height: 60px; margin-bottom: 10px;">
                            @endif
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">{{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }}</h1>
                            <p style="color: #dcfce7; margin: 5px 0 0; font-size: 14px;">{{ $resortInfo->address ?? '' }}</p>
                        </td>
                    </tr>

                    <!-- Confirmation Banner -->
                    <tr>
                        <td style="background-color: #dcfce7; padding: 20px; text-align: center;">
                            <h2 style="color: #166534; margin: 0; font-size: 22px;">✓ Booking Confirmed!</h2>
                            <p style="color: #166534; margin: 10px 0 0; font-size: 14px;">Booking Reference: <strong>#{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</strong></p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 25px;">
                            <p style="font-size: 16px; color: #333; margin: 0 0 20px;">
                                Dear <strong>{{ $booking->customer_name }}</strong>,
                            </p>
                            <p style="font-size: 14px; color: #555; margin: 0 0 20px; line-height: 1.6;">
                                Thank you for choosing {{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }}. Your booking has been confirmed. Below are the details of your reservation:
                            </p>

                            <!-- Booking Details Table -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold; width: 40%;">Check-in Date:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M, Y') }} (12:00 Noon)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold;">Check-out Date:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M, Y') }} (12:00 Noon)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold;">Total Nights:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }} Night(s)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold;">Guests:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $booking->number_of_guests }} Person(s)</td>
                                </tr>
                                @php
                                    $allRooms = $booking->getAllRooms();
                                @endphp
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold;">Room(s):</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">
                                        @if($allRooms->count() > 0)
                                            @foreach($allRooms as $room)
                                                Room {{ $room->room_number }} ({{ $room->roomType->name ?? 'Room' }})@if(!$loop->last), @endif
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Payment Summary -->
                            @php
                                $baseAmount = $booking->total_amount;
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
                                $remainingPayment = $grandTotal - $booking->advance_payment;
                            @endphp
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #fafafa; border: 1px solid #e0e0e0;">
                                <tr>
                                    <td colspan="2" style="padding: 12px; background-color: #166534; color: white; font-weight: bold; font-size: 16px;">Payment Summary</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0;">Room Charges:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">৳{{ number_format($baseAmount, 0) }}</td>
                                </tr>
                                @if($vatAmount > 0)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0;">VAT (15%):</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">৳{{ number_format($vatAmount, 0) }}</td>
                                </tr>
                                @endif
                                @if($extraCharges > 0)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0;">Extra Charges:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">৳{{ number_format($extraCharges, 0) }}</td>
                                </tr>
                                @endif
                                @if($discountAmount > 0)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; color: #dc2626;">Discount:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right; color: #dc2626;">- ৳{{ number_format($discountAmount, 0) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; font-weight: bold;">Grand Total:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right; font-weight: bold;">৳{{ number_format($grandTotal, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; color: #166534;">Advance Paid:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right; color: #166534;">৳{{ number_format($booking->advance_payment, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 12px; font-weight: bold; color: #dc2626;">Due Amount:</td>
                                    <td style="padding: 10px 12px; text-align: right; font-weight: bold; color: #dc2626;">৳{{ number_format($remainingPayment, 0) }}</td>
                                </tr>
                            </table>

                            <!-- Contact Info -->
                            <p style="font-size: 14px; color: #555; margin: 0 0 10px; line-height: 1.6;">
                                If you have any questions or need assistance, please contact us:
                            </p>
                            <p style="font-size: 14px; color: #333; margin: 0 0 20px;">
                                📞 Phone: {{ $resortInfo->phone ?? 'N/A' }}<br>
                                ✉️ Email: {{ $resortInfo->email ?? 'N/A' }}
                            </p>

                            <p style="font-size: 14px; color: #555; margin: 0; line-height: 1.6;">
                                We look forward to welcoming you!
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #333; padding: 20px; text-align: center;">
                            <p style="color: #ccc; margin: 0 0 10px; font-size: 14px;">{{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }}</p>
                            <p style="color: #888; margin: 0; font-size: 12px;">{{ $resortInfo->address ?? '' }}</p>
                            <p style="color: #888; margin: 10px 0 0; font-size: 12px;">© {{ date('Y') }} All Rights Reserved</p>
                            <p style="color: #666; margin: 10px 0 0; font-size: 10px;">Developed By Mir Javed Jeetu | 01811480222</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
