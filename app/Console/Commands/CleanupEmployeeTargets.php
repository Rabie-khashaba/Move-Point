<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeTarget;
use Illuminate\Support\Facades\DB;

class CleanupEmployeeTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee-targets:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate employee targets and ensure data integrity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🧹 Starting employee targets cleanup...');

        // Find duplicate targets
        $duplicates = DB::table('employee_targets')
            ->select('employee_id', 'year', 'month', DB::raw('COUNT(*) as count'))
            ->groupBy('employee_id', 'year', 'month')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate targets found!');
            return 0;
        }

        $this->warn("⚠️  Found {$duplicates->count()} duplicate target groups:");

        foreach ($duplicates as $duplicate) {
            $this->line("   - Employee ID: {$duplicate->employee_id}, Year: {$duplicate->year}, Month: {$duplicate->month} ({$duplicate->count} records)");
        }

        if ($isDryRun) {
            $this->info('🔍 DRY RUN: Would delete ' . ($duplicates->sum('count') - $duplicates->count()) . ' duplicate records');
            return 0;
        }

        if (!$this->confirm('Do you want to proceed with cleanup?')) {
            $this->info('❌ Cleanup cancelled');
            return 0;
        }

        $deletedCount = 0;
        $bar = $this->output->createProgressBar($duplicates->count());

        foreach ($duplicates as $duplicate) {
            // Keep the first record, delete the rest
            $targetsToDelete = EmployeeTarget::where('employee_id', $duplicate->employee_id)
                ->where('year', $duplicate->year)
                ->where('month', $duplicate->month)
                ->orderBy('id')
                ->skip(1)
                ->take($duplicate->count - 1)
                ->get();

            foreach ($targetsToDelete as $target) {
                $target->delete();
                $deletedCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Cleanup completed! Deleted {$deletedCount} duplicate records.");

        // Verify cleanup
        $remainingDuplicates = DB::table('employee_targets')
            ->select('employee_id', 'year', 'month', DB::raw('COUNT(*) as count'))
            ->groupBy('employee_id', 'year', 'month')
            ->having('count', '>', 1)
            ->count();

        if ($remainingDuplicates === 0) {
            $this->info('✅ All duplicates have been removed successfully!');
        } else {
            $this->warn("⚠️  Still found {$remainingDuplicates} duplicate groups after cleanup.");
        }

        return 0;
    }
}
