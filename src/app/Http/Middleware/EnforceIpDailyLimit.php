<?php

namespace App\Http\Middleware;

use App\Services\RateLimiting\RequestUsageLimiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceIpDailyLimit
{
    public function __construct(private readonly RequestUsageLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $limit = config('rate_limiting.register_ip_daily_limit');

        $result = $this->limiter->attempt('ip', (string) $request->ip(), $limit);

        if (!$result['allowed']) {
            return response()->json([
                'message' => 'Daily registration limit exceeded for this IP address.',
                'limit' => $result['limit'],
                'used' => $result['used'],
                'resets_at' => $result['resets_at'],
            ], 429);
        }

        return $next($request);
    }
}
