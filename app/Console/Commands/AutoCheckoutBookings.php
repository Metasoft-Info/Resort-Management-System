<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Carbon\Carbon;

class AutoCheckoutBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checkout bookings that are past their check-out date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Find bookings that should be checked out
        $bookings = Booking::where('status', 'checked_in')
            ->whereDate('check_out_date', '<', $today)
            ->get();

        $count = 0;
        foreach ($bookings as $booking) {
            $booking->update([
                'status' => 'checked_out'
            ]);
            $count++;
            
            $this->info("Auto checked-out booking #{$booking->id} - {$booking->customer_name}");
        }

        $this->info("Total bookings auto checked-out: {$count}");
        
        return Command::SUCCESS;
    }
}
