<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\RateLimiting\RequestUsageLimiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceUserDailyLimit
{
    public function __construct(private readonly RequestUsageLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return $next($request);
        }

        $override = $user->rateLimitOverride;

        if ($override && $override->unlimited) {
            $limit = null;
        } elseif ($override && $override->daily_limit !== null) {
            $limit = $override->daily_limit;
        } else {
            $limit = config('rate_limiting.default_daily_user_limit');
        }

        $result = $this->limiter->attempt('user', (string) $user->id, $limit);

        if (!$result['allowed']) {
            return response()->json([
                'message' => 'Daily API request limit exceeded.',
                'limit' => $result['limit'],
                'used' => $result['used'],
                'resets_at' => $result['resets_at'],
            ], 429);
        }

        return $next($request);
    }

    private function resolveUser(Request $request): ?User
    {
        $authUser = auth('api')->user();
        if ($authUser) {
            return $authUser;
        }

        $email = $request->input('email');
        if (is_string($email) && $email !== '') {
            return User::query()->where('email', $email)->first();
        }

        return null;
    }
}
