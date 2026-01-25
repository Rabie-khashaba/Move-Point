<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StorageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the request is for a storage file, serve it directly
        if (str_starts_with($request->getPathInfo(), '/storage/')) {
            $filePath = public_path($request->getPathInfo());
            
            if (file_exists($filePath) && is_file($filePath)) {
                return response()->file($filePath);
            }
        }

        return $next($request);
    }
}
