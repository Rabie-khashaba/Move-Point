<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Add performance headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Add cache headers for static assets
        if ($request->is('assets/*') || $request->is('css/*') || $request->is('js/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
        }
        
        // Add cache headers for API responses
        if ($request->is('api/*')) {
            $response->headers->set('Cache-Control', 'private, max-age=300');
        }
        
        return $response;
    }
}
