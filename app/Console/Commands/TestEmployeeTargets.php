<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmployeeTarget;
use App\Models\Employee;

class TestEmployeeTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee-targets:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the employee targets system to ensure it\'s working correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing employee targets system...');

        // Check if we have any sales employees
        $salesEmployees = Employee::active()->where('department_id', 7)->get();
        
        if ($salesEmployees->isEmpty()) {
            $this->error('❌ No sales employees found!');
            return 1;
        }

        $this->info("✅ Found {$salesEmployees->count()} sales employees");

        // Check current year and month
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $this->info("Current period: {$currentYear}-{$currentMonth}");

        // Check existing targets
        $existingTargets = EmployeeTarget::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->get();

        $this->info("Found {$existingTargets->count()} existing targets for current period");

        // Test creating a target
        $testEmployee = $salesEmployees->first();
        $this->info("Testing with employee: {$testEmployee->name}");

        // Check if target exists
        $existingTarget = EmployeeTarget::where('employee_id', $testEmployee->id)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        if ($existingTarget) {
            $this->info("Target exists with ID: {$existingTarget->id}");
            $this->info("Current target_follow_ups: {$existingTarget->target_follow_ups}");
            
            // Test updating the target
            $oldValue = $existingTarget->target_follow_ups;
            $newValue = $oldValue + 10;
            
            $this->info("Testing update: {$oldValue} -> {$newValue}");
            
            $existingTarget->update(['target_follow_ups' => $newValue]);
            $existingTarget->refresh();
            
            $this->info("After update, target_follow_ups: {$existingTarget->target_follow_ups}");
            
            if ($existingTarget->target_follow_ups == $newValue) {
                $this->info('✅ Target update test passed!');
            } else {
                $this->error('❌ Target update test failed!');
            }
            
            // Revert the change
            $existingTarget->update(['target_follow_ups' => $oldValue]);
            $this->info("Reverted target back to: {$oldValue}");
            
        } else {
            $this->info("No target exists, creating test target...");
            
            $newTarget = EmployeeTarget::create([
                'employee_id' => $testEmployee->id,
                'year' => $currentYear,
                'month' => $currentMonth,
                'target_follow_ups' => 50,
                'achieved_follow_ups' => 0,
                'notes' => 'Test target'
            ]);
            
            $this->info("✅ Created test target with ID: {$newTarget->id}");
            $this->info("Target value: {$newTarget->target_follow_ups}");
            
            // Test updating
            $newTarget->update(['target_follow_ups' => 75]);
            $newTarget->refresh();
            
            $this->info("After update, target value: {$newTarget->target_follow_ups}");
            
            if ($newTarget->target_follow_ups == 75) {
                $this->info('✅ Target update test passed!');
            } else {
                $this->error('❌ Target update test failed!');
            }
            
            // Clean up
            $newTarget->delete();
            $this->info("Cleaned up test target");
        }

        $this->info('🎉 Employee targets system test completed!');
        return 0;
    }
}
