<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAiApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey || $apiKey !== config('services.ai.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
