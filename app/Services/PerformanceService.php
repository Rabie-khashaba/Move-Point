<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PerformanceService
{
    /**
     * Cache frequently accessed data
     */
    public static function cacheData($key, $callback, $minutes = 300)
    {
        return Cache::remember($key, $minutes * 60, $callback);
    }

    /**
     * Clear specific cache keys
     */
    public static function clearCache($keys = [])
    {
        if (empty($keys)) {
            Cache::flush();
        } else {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Optimize database queries with chunking
     */
    public static function chunkQuery($query, $chunkSize = 1000, $callback = null)
    {
        $results = collect();
        
        $query->chunk($chunkSize, function ($chunk) use (&$results, $callback) {
            if ($callback) {
                $callback($chunk);
            } else {
                $results = $results->merge($chunk);
            }
        });
        
        return $results;
    }

    /**
     * Get database query statistics
     */
    public static function getQueryStats()
    {
        if (config('app.debug')) {
            return [
                'queries' => DB::getQueryLog(),
                'time' => microtime(true) - LARAVEL_START,
                'memory' => memory_get_peak_usage(true)
            ];
        }
        
        return null;
    }
}
