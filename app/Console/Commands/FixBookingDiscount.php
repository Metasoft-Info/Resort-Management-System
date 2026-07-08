<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Console\Command;

class FixBookingDiscount extends Command
{
    protected $signature = 'booking:fix-payment {booking_id : The booking ID} {amount_to_reduce : Amount to reduce from payments}';
    protected $description = 'Fix booking payment by reducing payment amount (e.g., for missed discount)';

    public function handle()
    {
        $bookingId = $this->argument('booking_id');
        $amountToReduce = (float) $this->argument('amount_to_reduce');

        $booking = Booking::find($bookingId);

        if (!$booking) {
            $this->error("Booking #{$bookingId} not found.");
            return 1;
        }

        $this->info("Booking #{$bookingId}: {$booking->customer_name}");
        $this->info("Current Total: " . $booking->getGrandTotal());
        $this->info("Current Deposited: " . $booking->getTotalDeposited());
        $this->info("Current Remaining: " . $booking->getCalculatedRemaining());
        $this->info("Amount to reduce: {$amountToReduce}");

        if (!$this->confirm("Do you want to proceed?")) {
            $this->info("Cancelled.");
            return 0;
        }

        // Get payments in reverse order (newest first)
        $payments = $booking->payments()->orderBy('created_at', 'desc')->get();
        $remainingToReduce = $amountToReduce;

        foreach ($payments as $payment) {
            if ($remainingToReduce <= 0) break;

            $currentAmount = $payment->amount;
            $newAmount = max(0, $currentAmount - $remainingToReduce);
            $reducedFromThis = $currentAmount - $newAmount;

            if ($reducedFromThis > 0) {
                $payment->amount = $newAmount;
                $payment->save();
                $remainingToReduce -= $reducedFromThis;
                $this->info("Reduced {$reducedFromThis} from payment #{$payment->id} (was {$currentAmount}, now {$newAmount})");
            }
        }

        if ($remainingToReduce > 0) {
            $this->warn("Could not reduce full amount. Still remaining: {$remainingToReduce}");
        }

        // Update booking payment status
        $booking->payment_status = $booking->getCalculatedPaymentStatus();
        $booking->save();

        $this->info("Done. New deposited: " . $booking->getTotalDeposited());
        $this->info("New remaining: " . $booking->getCalculatedRemaining());

        return 0;
    }
}
