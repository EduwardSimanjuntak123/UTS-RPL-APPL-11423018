<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MicroserviceTokenMiddleware
{
    /**
     * Handle incoming API requests with microservice token validation
     */
    public function handle(Request $request, Closure $next)
    {
        // Get authorization header
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json([
                'message' => 'Unauthorized - No token provided',
                'status' => 'error',
            ], 401);
        }

        // Verify token format
        if (!preg_match('/^Bearer\s/', $authHeader)) {
            return response()->json([
                'message' => 'Unauthorized - Invalid token format',
                'status' => 'error',
            ], 401);
        }

        // Store token in session for API client to use
        $token = str_replace('Bearer ', '', $authHeader);
        session(['api_token' => $token]);

        // Get user info from request if available
        $userInfo = $request->get('user');
        if ($userInfo) {
            session(['user' => $userInfo]);
        }

        return $next($request);
    }
}
