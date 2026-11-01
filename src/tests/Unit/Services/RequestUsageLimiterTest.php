<?php

namespace Tests\Unit\Services;

use App\Services\RateLimiting\RequestUsageLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RequestUsageLimiterTest extends TestCase
{
    use RefreshDatabase;

    // Test 1: con limit=null nunca bloquea, sin importar cuántos intentos
    public function test_attempt_with_null_limit_never_blocks(): void
    {
        $limiter = new RequestUsageLimiter();

        for ($i = 0; $i < 5; $i++) {
            $result = $limiter->attempt('user', '1', null);
            $this->assertTrue($result['allowed']);
        }
    }

    // Test 2: forma de retorno correcta al permitir y al bloquear
    public function test_attempt_returns_expected_shape(): void
    {
        $limiter = new RequestUsageLimiter();

        $allowed = $limiter->attempt('user', '2', 1);
        $this->assertSame(['allowed', 'used', 'limit', 'resets_at'], array_keys($allowed));
        $this->assertTrue($allowed['allowed']);
        $this->assertSame(1, $allowed['used']);
        $this->assertSame(1, $allowed['limit']);

        $blocked = $limiter->attempt('user', '2', 1);
        $this->assertFalse($blocked['allowed']);
        $this->assertSame(1, $blocked['used']);
    }

    // Test 3: resets_at siempre es la próxima medianoche UTC, sin importar la hora actual
    public function test_resets_at_is_always_next_utc_midnight(): void
    {
        $limiter = new RequestUsageLimiter();

        $this->travelTo(Carbon::parse('2026-08-24 08:15:00', 'UTC'));
        $result = $limiter->attempt('user', '3', null);
        $this->assertSame('2026-08-25T00:00:00+00:00', $result['resets_at']);

        $this->travelTo(Carbon::parse('2026-08-24 23:59:59', 'UTC'));
        $result = $limiter->attempt('user', '4', null);
        $this->assertSame('2026-08-25T00:00:00+00:00', $result['resets_at']);
    }

    // Test 4: llamadas secuenciales el mismo día incrementan correctamente sin duplicar filas
    public function test_sequential_calls_same_day_increment_without_duplicating_row(): void
    {
        $limiter = new RequestUsageLimiter();

        $limiter->attempt('ip', '10.0.0.1', null);
        $limiter->attempt('ip', '10.0.0.1', null);
        $result = $limiter->attempt('ip', '10.0.0.1', null);

        $this->assertSame(3, $result['used']);
        $this->assertDatabaseCount('api_usage_counters', 1);
    }
}
