<?php

namespace App\Console\Commands;

use App\Models\Reservations;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteReservations extends Command
{
    protected $signature = 'reservations:complete';
    
    //protected $description = 'Mark completed reservations where check-out date has passed';

    public function handle()
    {
        
        $yesterday = Carbon::yesterday();
        
        $reservations = Reservations::where('status', 'confirmed')
            ->whereDate('check_out', '<=', $yesterday)
            ->get();
        
        $count = 0;
        foreach ($reservations as $reservation) {
            $reservation->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
            $count++;
        }
        
        $this->info("Completed {$count} reservations.");
        
        
        return Command::SUCCESS;
    }
}
