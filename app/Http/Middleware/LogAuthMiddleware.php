<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            Log::info('User authenticated', [
                'user' => $user->email,
                'role' => $user->role,
                'route' => $request->route()->getName()
            ]);
        } else {
            Log::warning('Unauthenticated access attempt', [
                'route' => $request->route()->getName(),
                'ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}