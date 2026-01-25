<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixEmployeeTargetsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee-targets:fix-table {--force : Force the fix without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the employee targets table structure to ensure it has the correct columns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Checking employee targets table structure...');

        // Check if table exists
        if (!Schema::hasTable('employee_targets')) {
            $this->error('❌ employee_targets table does not exist!');
            $this->info('Creating the table...');
            
            if (!$this->option('force') && !$this->confirm('Do you want to create the employee_targets table?')) {
                $this->info('❌ Operation cancelled');
                return 1;
            }

            // Create the table with correct structure
            Schema::create('employee_targets', function ($table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->onDelete('cascade');
                $table->integer('year');
                $table->integer('month');
                $table->integer('target_follow_ups')->default(0);
                $table->integer('achieved_follow_ups')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->unique(['employee_id', 'year', 'month']);
                $table->index(['year', 'month']);
            });

            $this->info('✅ employee_targets table created successfully!');
            return 0;
        }

        // Check current columns
        $columns = Schema::getColumnListing('employee_targets');
        $this->info('Current columns: ' . implode(', ', $columns));

        $needsFix = false;
        $fixes = [];

        // Check if we have the old columns
        if (in_array('target_amount', $columns) || in_array('achieved_amount', $columns)) {
            $needsFix = true;
            $fixes[] = 'Remove old target_amount/achieved_amount columns';
        }

        // Check if we have the new columns
        if (!in_array('target_follow_ups', $columns)) {
            $needsFix = true;
            $fixes[] = 'Add target_follow_ups column';
        }

        if (!in_array('achieved_follow_ups', $columns)) {
            $needsFix = true;
            $fixes[] = 'Add achieved_follow_ups column';
        }

        if (!$needsFix) {
            $this->info('✅ Table structure is correct!');
            return 0;
        }

        $this->warn('⚠️  Table structure needs fixing:');
        foreach ($fixes as $fix) {
            $this->line("   - {$fix}");
        }

        if (!$this->option('force') && !$this->confirm('Do you want to fix the table structure?')) {
            $this->info('❌ Operation cancelled');
            return 1;
        }

        $this->info('🔧 Fixing table structure...');

        try {
            DB::beginTransaction();

            // Add new columns if they don't exist
            if (!in_array('target_follow_ups', $columns)) {
                Schema::table('employee_targets', function ($table) {
                    $table->integer('target_follow_ups')->default(0)->after('month');
                });
                $this->info('✅ Added target_follow_ups column');
            }

            if (!in_array('achieved_follow_ups', $columns)) {
                Schema::table('employee_targets', function ($table) {
                    $table->integer('achieved_follow_ups')->default(0)->after('target_follow_ups');
                });
                $this->info('✅ Added achieved_follow_ups column');
            }

            // Remove old columns if they exist
            if (in_array('target_amount', $columns)) {
                Schema::table('employee_targets', function ($table) {
                    $table->dropColumn('target_amount');
                });
                $this->info('✅ Removed target_amount column');
            }

            if (in_array('achieved_amount', $columns)) {
                Schema::table('employee_targets', function ($table) {
                    $table->dropColumn('achieved_amount');
                });
                $this->info('✅ Removed achieved_amount column');
            }

            // Add unique constraint if it doesn't exist
            $indexes = DB::select("SHOW INDEX FROM employee_targets WHERE Key_name = 'employee_targets_employee_id_year_month_unique'");
            if (empty($indexes)) {
                Schema::table('employee_targets', function ($table) {
                    $table->unique(['employee_id', 'year', 'month']);
                });
                $this->info('✅ Added unique constraint');
            }

            DB::commit();
            $this->info('✅ Table structure fixed successfully!');

            // Show final structure
            $finalColumns = Schema::getColumnListing('employee_targets');
            $this->info('Final columns: ' . implode(', ', $finalColumns));

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error fixing table: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
