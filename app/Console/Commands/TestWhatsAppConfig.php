<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestWhatsAppConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:test-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp configuration and connectivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing WhatsApp Configuration...');
        
        // Test environment variables
        $this->info("\n📋 Environment Variables:");
        $this->line("WHATSAPP_SERVICE: " . (env('WHATSAPP_SERVICE') ?: 'NOT SET'));
        $this->line("WAPILOT_INSTANCE_ID: " . (env('WAPILOT_INSTANCE_ID') ?: 'NOT SET'));
        $this->line("WAPILOT_API_TOKEN: " . (env('WAPILOT_API_TOKEN') ? 'SET' : 'NOT SET'));
        $this->line("WAPILOT_BASE_URL: " . (env('WAPILOT_BASE_URL') ?: 'NOT SET'));
        
        // Test config values
        $this->info("\n⚙️ Config Values:");
        $this->line("services.whatsapp.service: " . config('services.whatsapp.service'));
        $this->line("services.whatsapp.wapilot_instance_id: " . config('services.whatsapp.wapilot_instance_id'));
        $this->line("services.whatsapp.wapilot_api_token: " . (config('services.whatsapp.wapilot_api_token') ? 'SET' : 'NOT SET'));
        $this->line("services.whatsapp.wapilot_base_url: " . config('services.whatsapp.wapilot_base_url'));
        
        // Test if configuration is valid
        $this->info("\n🔧 Configuration Validation:");
        
        $service = config('services.whatsapp.service');
        $instanceId = config('services.whatsapp.wapilot_instance_id');
        $apiToken = config('services.whatsapp.wapilot_api_token');
        
        if ($service === 'wapilot') {
            if ($instanceId && $apiToken) {
                $this->info("✅ WAPilot configuration is valid");
                
                // Test API connectivity
                $this->info("\n🌐 Testing API Connectivity:");
                $this->testWAPilotConnectivity();
            } else {
                $this->error("❌ WAPilot configuration is incomplete");
                $this->line("Missing: " . (!$instanceId ? 'WAPILOT_INSTANCE_ID' : '') . (!$apiToken ? ' WAPILOT_API_TOKEN' : ''));
            }
        } else {
            $this->warn("⚠️ WhatsApp service is set to: {$service}");
            if ($service === 'simulation') {
                $this->line("Messages will be logged but not sent");
            }
        }
        
        // Test WhatsApp service
        $this->info("\n📱 Testing WhatsApp Service:");
        $this->testWhatsAppService();
        
        return 0;
    }
    
    private function testWAPilotConnectivity()
    {
        try {
            $instanceId = config('services.whatsapp.wapilot_instance_id');
            $apiToken = config('services.whatsapp.wapilot_api_token');
            $baseUrl = config('services.whatsapp.wapilot_base_url', 'https://wapilot.net/api');
            
            // Test the actual send-message endpoint with a test message
            $url = "{$baseUrl}/v1/{$instanceId}/send-message";
            
            $this->line("Testing URL: {$url}");
            
            $data = [
                'token' => $apiToken,
                'chat_id' => '201234567890',
                'text' => 'Test message from server configuration check'
            ];
            
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $data);
            
            if ($response->successful()) {
                $this->info("✅ WAPilot API is accessible and working");
                $this->line("Response: " . $response->body());
            } else {
                $this->error("❌ WAPilot API returned status: " . $response->status());
                $this->line("Response: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->error("❌ WAPilot API connectivity failed: " . $e->getMessage());
        }
    }
    
    private function testWhatsAppService()
    {
        try {
            $whatsappService = new \App\Services\WhatsAppService();
            $result = $whatsappService->send('201234567890', 'Test message from server');
            
            if ($result) {
                $this->info("✅ WhatsApp service test successful");
            } else {
                $this->error("❌ WhatsApp service test failed");
            }
        } catch (\Exception $e) {
            $this->error("❌ WhatsApp service error: " . $e->getMessage());
        }
    }
}
