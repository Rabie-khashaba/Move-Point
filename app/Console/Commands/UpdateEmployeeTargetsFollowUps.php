<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeTarget;

class UpdateEmployeeTargetsFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee-targets:update-follow-ups {--year= : Specific year to update} {--month= : Specific month to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update employee targets achieved follow-ups based on actual follow-ups made';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year') ?? now()->year;
        $month = $this->option('month') ?? now()->month;

        $this->info("Updating employee targets for {$year}-{$month}...");

        $targets = EmployeeTarget::where('year', $year)
            ->when($month, function($query, $month) {
                return $query->where('month', $month);
            })
            ->get();

        if ($targets->isEmpty()) {
            $this->warn("No targets found for {$year}-{$month}");
            return;
        }

        $updatedCount = 0;
        $bar = $this->output->createProgressBar($targets->count());

        foreach ($targets as $target) {
            $oldValue = $target->achieved_follow_ups;
            $target->updateAchievedFollowUps();
            
            if ($oldValue != $target->achieved_follow_ups) {
                $updatedCount++;
                $this->line("\nUpdated target for {$target->employee->name}: {$oldValue} → {$target->achieved_follow_ups}");
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Updated {$updatedCount} targets out of {$targets->count()} total targets.");
    }
}
