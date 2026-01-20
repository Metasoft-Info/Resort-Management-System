<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConventionBooking;
use Carbon\Carbon;

class UpdateConventionStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conventions:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically update convention booking statuses based on event dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $count = 0;

        // Update confirmed bookings to completed if event date has passed
        $bookings = ConventionBooking::where('status', 'confirmed')
            ->whereDate('event_date', '<', $today)
            ->get();

        foreach ($bookings as $booking) {
            $booking->update([
                'status' => 'completed',
                'program_status' => 'completed'
            ]);
            $count++;
            
            $this->info("Auto completed convention booking #{$booking->id} - {$booking->customer_name}");
        }

        $this->info("Total convention bookings auto completed: {$count}");
        
        return Command::SUCCESS;
    }
}
