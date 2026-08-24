<?php

namespace Tests\Unit;

use App\Models\BookingPayment;
use PHPUnit\Framework\TestCase;

class BookingPaymentIdempotencyTest extends TestCase
{
    public function test_payment_request_id_is_mass_assignable_for_retry_protection(): void
    {
        $requestId = '6ba7b810-9dad-41d1-80b4-00c04fd430c8';
        $payment = new BookingPayment(['request_id' => $requestId]);

        $this->assertSame($requestId, $payment->request_id);
    }

    public function test_room_payment_endpoint_uses_a_transaction_and_row_lock(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2).'/app/Http/Controllers/BookingController.php');

        $this->assertNotFalse($source);
        $this->assertStringContainsString('DB::transaction', $source);
        $this->assertStringContainsString('->lockForUpdate()', $source);
        $this->assertStringContainsString("firstWhere('request_id', \$paymentRequestId)", $source);
    }
}
