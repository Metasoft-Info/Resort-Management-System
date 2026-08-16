<?php

namespace Tests\Unit;

use App\Http\Controllers\ConventionBookingController;
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
}
