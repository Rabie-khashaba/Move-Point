<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class ResetEmployeeMonthlyData extends Command
{
    protected $signature = 'employees:reset-monthly {--force : Run without confirmation}';
    protected $description = 'Reset employee monthly fields (bonus, deductions, total_salary) at the start of each month';

    public function handle()
    {
        $this->info('🧮 Starting monthly reset for employee salaries...');

        if (!$this->option('force') && !$this->confirm('Do you want to reset monthly employee data?')) {
            $this->info('❌ Operation cancelled.');
            return 1;
        }

        try {
            DB::beginTransaction();

            // Reset all employees’ monthly-related fields
            Employee::query()->update([
                'bonus_amount' => 0,
                'deductions'   => 0,
                'notes'        => null,
                'total_salary' => DB::raw('salary'),
            ]);

            DB::commit();
            $this->info('✅ All employee monthly data reset successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error during reset: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
