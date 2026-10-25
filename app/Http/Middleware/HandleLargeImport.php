<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLargeImport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Konfigurasi untuk menangani file import yang sangat besar
        ini_set('memory_limit', '2048M'); // 2GB memory
        ini_set('max_execution_time', 0); // Unlimited execution time
        ini_set('max_input_time', 0); // Unlimited input time
        ini_set('post_max_size', '100M'); // Max POST size
        ini_set('upload_max_filesize', '100M'); // Max file upload size

        // Set timezone untuk menghindari warning
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }

        return $next($request);
    }
}
