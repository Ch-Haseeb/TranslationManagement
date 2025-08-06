<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Authentication required',
            ], Response::HTTP_UNAUTHORIZED);
        }

      
        $validTokens = config('app.api_tokens', ['apiToken@12345']);

        if (!in_array($token, $validTokens)) {
            return response()->json([
                'message' => 'Invalid authentication token',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
