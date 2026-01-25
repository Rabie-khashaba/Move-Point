<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ModuleNameService;

class AddModuleName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:add {module} {arabic_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new module name to the ModuleNameService';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module');
        $arabicName = $this->argument('arabic_name');

        $this->info("Adding module: {$module} -> {$arabicName}");

        // Read the current service file
        $servicePath = app_path('Services/ModuleNameService.php');
        $content = file_get_contents($servicePath);

        // Find the arabicNames array
        $pattern = '/\$arabicNames\s*=\s*\[(.*?)\];/s';
        if (preg_match($pattern, $content, $matches)) {
            $arrayContent = $matches[1];
            
            // Add the new module name
            $newEntry = "            '{$module}' => '{$arabicName}',";
            
            // Find the last entry and add after it
            $lines = explode("\n", $arrayContent);
            $lastEntryIndex = -1;
            
            for ($i = count($lines) - 1; $i >= 0; $i--) {
                if (trim($lines[$i]) && !str_contains($lines[$i], 'other')) {
                    $lastEntryIndex = $i;
                    break;
                }
            }
            
            if ($lastEntryIndex >= 0) {
                array_splice($lines, $lastEntryIndex + 1, 0, $newEntry);
            } else {
                // If no entries found, add at the beginning
                array_unshift($lines, $newEntry);
            }
            
            $newArrayContent = implode("\n", $lines);
            $newContent = preg_replace($pattern, "\$arabicNames = [{$newArrayContent}];", $content);
            
            // Write back to file
            file_put_contents($servicePath, $newContent);
            
            $this->info("✅ Module '{$module}' added successfully!");
            $this->info("Arabic name: {$arabicName}");
        } else {
            $this->error("❌ Could not find arabicNames array in ModuleNameService.php");
        }
    }
}
