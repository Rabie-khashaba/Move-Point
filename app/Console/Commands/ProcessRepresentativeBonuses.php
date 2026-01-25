<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RepresentativeTarget;

class ProcessRepresentativeBonuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'representatives:process-bonuses {--year=} {--month=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process bonuses for employees who reached representative conversion targets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year') ?? now()->year;
        $month = $this->option('month') ?? now()->month;

        $this->info("Processing representative bonuses for {$year}/{$month}");

        // Process all targets for the specified month/year
        $processedCount = RepresentativeTarget::processAllTargets($year, $month);

        if ($processedCount > 0) {
            $this->info("Processed {$processedCount} targets and awarded bonuses to qualified employees!");
        } else {
            $this->warn("No representative targets found for {$year}/{$month}");
        }

        $this->info("Bonus processing completed!");
    }
}