<?php

namespace Tests\Unit;

use App\Http\Controllers\ConventionBookingController;
use App\Models\ConventionBooking;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ConventionBookingFinancialsTest extends TestCase
{
    private function calculateTotals(array $data): array
    {
        $method = new ReflectionMethod(ConventionBookingController::class, 'calculateTotals');
        $method->setAccessible(true);

        return $method->invoke(new ConventionBookingController(), $data);
    }

    private function calculateBookingFinancials(ConventionBooking $booking): array
    {
        $method = new ReflectionMethod(ConventionBookingController::class, 'calculateBookingFinancials');
        $method->setAccessible(true);

        return $method->invoke(new ConventionBookingController(), $booking);
    }

    public function test_vat_is_zero_when_the_checkbox_is_not_enabled(): void
    {
        $totals = $this->calculateTotals([
            'hall_rent' => 1000,
            'vat_enabled' => false,
            'vat_percentage' => 15,
        ]);

        $this->assertEquals(0, $totals['vat_amount']);
        $this->assertEquals(1000, $totals['total_amount']);
    }

    public function test_enabled_vat_is_calculated_after_discount(): void
    {
        $totals = $this->calculateTotals([
            'hall_rent' => 1000,
            'discount' => 100,
            'vat_enabled' => true,
            'vat_percentage' => 15,
        ]);

        $this->assertEquals(135, $totals['vat_amount']);
        $this->assertEquals(1035, $totals['total_amount']);
    }

    public function test_disabled_vat_ignores_a_legacy_stored_vat_amount(): void
    {
        $booking = new ConventionBooking([
            'hall_rent' => 50000,
            'addons_cost' => 1320,
            'discount' => 5000,
            'vat_enabled' => false,
            'vat_percentage' => 0,
            'vat_amount' => 3750,
            'advance_payment' => 0,
        ]);

        $totals = $this->calculateBookingFinancials($booking);

        $this->assertEquals(0, $totals['vat_amount']);
        $this->assertEquals(46320, $totals['total_amount']);
        $this->assertEquals(46320, $totals['remaining_payment']);
    }
}
