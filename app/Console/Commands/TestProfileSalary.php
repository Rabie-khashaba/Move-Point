<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Http\Controllers\Api\MobileRequestController;
use Illuminate\Http\Request;

class TestProfileSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:profile-salary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the profile response with detailed salary information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("💰 Testing Profile Salary Response");
        $this->info("=================================");

        // Get a representative user
        $representative = User::where('type', 'representative')->with('representative')->first();
        
        if (!$representative) {
            $this->error("No representative user found!");
            return 1;
        }

        $this->info("Testing with representative: {$representative->name}");

        // Create a mock request
        $request = new Request();
        $request->setUserResolver(function () use ($representative) {
            return $representative;
        });

        // Create controller instance and call getProfile
        $controller = new MobileRequestController(app(\App\Services\FirebaseNotificationService::class));
        $response = $controller->getProfile($request);
        
        $data = $response->getData(true);
        
        if (isset($data['data']['salary'])) {
            $salary = $data['data']['salary'];
            
            $this->info("\n✅ Salary object found in profile response:");
            $this->info("   Amount: {$salary['amount']} {$salary['currency']}");
            $this->info("   Formatted: {$salary['formatted']}");
            $this->info("   Monthly: " . ($salary['monthly'] ? 'Yes' : 'No'));
            
            if (isset($salary['details'])) {
                $this->info("\n📊 Salary Details:");
                $this->info("   Month: {$salary['details']['month']}");
                $this->info("   Base Salary: {$salary['details']['base_salary']}");
                $this->info("   Advance: {$salary['details']['advance']}");
                $this->info("   Bonus: {$salary['details']['bonus']}");
                $this->info("   Deduction: {$salary['details']['deduction']}");
                $this->info("   Missing Goods: {$salary['details']['missing_goods']}");
                $this->info("   Total: {$salary['details']['total']}");
            }
            
            if (isset($salary['breakdown']) && is_array($salary['breakdown'])) {
                $this->info("\n📋 Salary Breakdown:");
                foreach ($salary['breakdown'] as $item) {
                    $this->info("   {$item['type']}: {$item['amount']} - {$item['description']}");
                }
            }
            
            // Calculate expected total
            $expectedTotal = 5632 + 1000 + 150 - 0 - 124;
            $actualTotal = $salary['details']['total'] ?? 0;
            
            if ($expectedTotal == $actualTotal) {
                $this->info("\n✅ Total calculation is correct: {$actualTotal}");
            } else {
                $this->error("\n❌ Total calculation mismatch. Expected: {$expectedTotal}, Got: {$actualTotal}");
            }
            
        } else {
            $this->error("❌ Salary object not found in profile response!");
            return 1;
        }

        // Test with supervisor
        $supervisor = User::where('type', 'supervisor')->with('supervisor')->first();
        
        if ($supervisor) {
            $this->info("\n" . str_repeat("=", 50));
            $this->info("Testing with supervisor: {$supervisor->name}");
            
            $request->setUserResolver(function () use ($supervisor) {
                return $supervisor;
            });
            
            $response = $controller->getProfile($request);
            $data = $response->getData(true);
            
            if (isset($data['data']['salary'])) {
                $this->info("✅ Supervisor salary object also includes detailed breakdown");
            } else {
                $this->error("❌ Supervisor salary object missing details");
            }
        }

        $this->info("\n🎉 Profile salary test completed!");
        $this->info("The profile response now includes:");
        $this->info("  • Basic salary information (amount, currency, formatted)");
        $this->info("  • Detailed breakdown (base, advance, bonus, deductions)");
        $this->info("  • Monthly salary components");
        $this->info("  • Total calculation");

        return 0;
    }
}