<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Invoice</title>
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

                    <!-- Thank You Banner -->
                    <tr>
                        <td style="background-color: #dbeafe; padding: 20px; text-align: center;">
                            <h2 style="color: #1e40af; margin: 0; font-size: 22px;">🙏 Thank You for Staying with Us!</h2>
                            <p style="color: #1e40af; margin: 10px 0 0; font-size: 14px;">Invoice #{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 25px;">
                            <p style="font-size: 16px; color: #333; margin: 0 0 20px;">
                                Dear <strong>{{ $booking->customer_name }}</strong>,
                            </p>
                            <p style="font-size: 14px; color: #555; margin: 0 0 20px; line-height: 1.6;">
                                Thank you for choosing {{ $resortInfo->resort_name ?? 'Tufan Convention Resort' }} for your stay. We hope you had a wonderful experience. Here is a summary of your stay and invoice:
                            </p>

                            <!-- Stay Details Table -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold; width: 40%;">Check-in Date:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold;">Check-out Date:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #f8f8f8; border: 1px solid #e0e0e0; font-weight: bold;">Total Nights:</td>
                                    <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }} Night(s)</td>
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

                            <!-- Invoice Summary -->
                            @php
                                $invoiceNights = \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date));
                                $invoiceNights = max(1, $invoiceNights);
                                $invoiceBaseAmount = $booking->getCalculatedTotal();
                                $invoiceDiscountAmount = 0;
                                
                                if($booking->discount_type === 'percentage' && $booking->discount_percentage > 0) {
                                    $invoiceDiscountAmount = ($invoiceBaseAmount * $booking->discount_percentage) / 100;
                                } elseif($booking->discount_type === 'flat' && $booking->discount_amount > 0) {
                                    $invoiceDiscountAmount = $booking->discount_amount;
                                }
                                
                                $invoiceAfterDiscount = $invoiceBaseAmount - $invoiceDiscountAmount;
                                $invoiceExtraCharges = $booking->extra_charges ?? 0;
                                $invoiceVatAmount = $booking->vat_enabled ? ($invoiceAfterDiscount * 0.15) : 0;
                                $invoiceGrandTotal = $invoiceAfterDiscount + $invoiceExtraCharges + $invoiceVatAmount;
                                $invoiceRemainingPayment = $invoiceGrandTotal - $booking->advance_payment;
                            @endphp
                            
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background-color: #fafafa; border: 1px solid #e0e0e0;">
                                <tr>
                                    <td colspan="2" style="padding: 12px; background-color: #166534; color: white; font-weight: bold; font-size: 16px;">Invoice Summary</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0;">Room Charges:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">BDT {{ number_format($invoiceBaseAmount, 0) }}</td>
                                </tr>
                                @if($invoiceExtraCharges > 0)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0;">Extra Charges @if($booking->extra_charges_description)({{ $booking->extra_charges_description }})@endif:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">BDT {{ number_format($invoiceExtraCharges, 0) }}</td>
                                </tr>
                                @endif
                                @if($invoiceVatAmount > 0)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0;">VAT (15%):</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">BDT {{ number_format($invoiceVatAmount, 0) }}</td>
                                </tr>
                                @endif
                                @if($invoiceDiscountAmount > 0)
                                <tr>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; color: #dc2626;">Discount @if($booking->discount_type === 'percentage')({{ $booking->discount_percentage }}%)@endif:</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #e0e0e0; text-align: right; color: #dc2626;">- BDT {{ number_format($invoiceDiscountAmount, 0) }}</td>
                                </tr>
                                @endif
                                <tr style="background-color: #dcfce7;">
                                    <td style="padding: 12px; font-weight: bold;">Grand Total:</td>
                                    <td style="padding: 12px; text-align: right; font-weight: bold; color: #166534; font-size: 18px;">BDT {{ number_format($invoiceGrandTotal, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 12px; border-top: 1px solid #e0e0e0;">Advance Paid:</td>
                                    <td style="padding: 10px 12px; border-top: 1px solid #e0e0e0; text-align: right; color: #166534;">BDT {{ number_format($booking->advance_payment, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; font-weight: bold; background-color: #fef2f2;">Amount Paid at Checkout:</td>
                                    <td style="padding: 12px; text-align: right; font-weight: bold; background-color: #fef2f2; color: #166534;">BDT {{ number_format($invoiceRemainingPayment, 0) }}</td>
                                </tr>
                            </table>

                            <!-- Thank You Message -->
                            <div style="background-color: #fef3c7; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                                <p style="font-size: 14px; color: #92400e; margin: 0; line-height: 1.6;">
                                    ⭐ We hope you enjoyed your stay! Your feedback is valuable to us. We look forward to welcoming you again.
                                </p>
                            </div>

                            <!-- Contact Info -->
                            <p style="font-size: 14px; color: #555; margin: 0 0 10px; line-height: 1.6;">
                                For any queries or future bookings, please contact us:
                            </p>
                            <p style="font-size: 14px; color: #333; margin: 0;">
                                📞 Phone: {{ $resortInfo->phone ?? 'N/A' }}<br>
                                ✉️ Email: {{ $resortInfo->email ?? 'N/A' }}
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
