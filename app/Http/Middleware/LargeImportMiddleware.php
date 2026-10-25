<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LargeImportMiddleware
{
    /**
     * Handle an incoming request for large imports.
     * Increases execution time and memory limits.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set execution time to 15 minutes (900 seconds)
        set_time_limit(900);

        // Increase memory limit to 512MB
        ini_set('memory_limit', '512M');

        // Increase max input time for file uploads
        ini_set('max_input_time', '900');

        // Disable output buffering to prevent timeout issues
        if (ob_get_level()) {
            ob_end_clean();
        }

        return $next($request);
    }
}
