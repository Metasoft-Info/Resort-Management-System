<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;

class ReturningGuestService
{
    public static function profile(Booking $booking): array
    {
        return $booking->only([
            'customer_name', 'customer_nid', 'customer_phone', 'customer_whatsapp',
            'customer_email', 'passport_number', 'customer_address', 'company_name',
            'reference_name', 'reference_phone',
        ]);
    }

    public static function phoneVariants(string $input): array
    {
        $digits = preg_replace('/\D/', '', $input);
        if (preg_match('/^(?:0088|88)?(01\d{9})$/', $digits, $matches)) {
            return [$matches[1], '88'.$matches[1], '0088'.$matches[1]];
        }

        return $digits !== '' ? [$digits] : [];
    }

    public function search(string $input): array
    {
        $query = self::forPhone($input)->orWhere('customer_nid', $input)->orWhere('passport_number', $input);

        $total = (clone $query)->count();
        $bookings = $query->with([
            'room.roomType', 'bookingRooms.room.roomType', 'roomShifts.fromRoom.roomType', 'payments',
        ])->orderByDesc('check_in_date')->orderByDesc('id')->limit(6)->get();

        if ($bookings->isEmpty()) {
            return ['success' => false, 'message' => 'No previous bookings found', 'history' => []];
        }

        return [
            'success' => true,
            'customer' => self::profile($bookings->first()),
            'total_bookings' => $total,
            'profile_url' => route('admin.customers.show', ['phone' => $bookings->first()->customer_phone]),
            'history' => $bookings->map(function (Booking $booking) {
                $financials = $booking->getFinancialBreakdown();

                return [
                    'id' => $booking->id,
                    'profile' => self::profile($booking),
                    'rooms' => $booking->getRoomBreakdown()->map(
                        fn ($line) => (string) ($line['room_number'] ?? $line['room']?->room_number ?? '')
                    )->filter()->unique()->values()->all(),
                    'check_in_date' => $booking->check_in_date->toDateString(),
                    'check_out_date' => $booking->check_out_date->toDateString(),
                    'nights' => $booking->getNights(),
                    'status' => $booking->status,
                    'bill_total' => $financials['grand_total'],
                    'paid' => $financials['deposited'],
                    'due' => $financials['remaining'],
                    'view_url' => route('admin.bookings.show', $booking),
                ];
            })->all(),
        ];
    }

    public static function forPhone(string $input): Builder
    {
        return Booking::query()->where(function ($query) use ($input) {
            $query->where('customer_phone', $input);
            $variants = self::phoneVariants($input);
            if ($variants) {
                // Match complete numbers in common local/country-code formats.
                $phoneSql = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(customer_phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', ''), '.', '')";
                $query->orWhereRaw($phoneSql.' IN ('.implode(',', array_fill(0, count($variants), '?')).')', $variants);
            }
        });
    }
}
