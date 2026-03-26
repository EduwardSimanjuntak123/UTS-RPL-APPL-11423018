<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * ⚠️ MICROSERVICES MODE: Authentication is handled by API Gateway
     * This middleware is disabled to prevent direct database access.
     * All user role checks must be done via Go services.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ✅ Skip middleware - auth handled by API Gateway + JWT tokens
        // ❌ NEVER query Auth::check() or Auth::user() - no database access!
        Log::info('CheckRole middleware bypassed - auth via API Gateway');
        
        // All authorization checks must happen in Go microservices
        return $next($request);
    }
}
