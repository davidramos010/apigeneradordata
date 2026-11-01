<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRateLimitOverride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    private function authHeadersFor(User $user): array
    {
        $token = auth('api')->login($user);

        return ['Authorization' => "Bearer {$token}"];
    }

    // Test 1: usuario por debajo del límite recibe 200 en cada petición
    public function test_user_under_limit_can_make_requests(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 3]);
        $user = User::factory()->create(['role' => 'user']);
        $headers = $this->authHeadersFor($user);

        for ($i = 0; $i < 3; $i++) {
            $this->getJson('/api/me', $headers)->assertStatus(200);
        }
    }

    // Test 2: usuario que llega al límite recibe 429 con el cuerpo correcto
    public function test_user_at_limit_receives_429_with_correct_body(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 2]);
        $user = User::factory()->create(['role' => 'user']);
        $headers = $this->authHeadersFor($user);

        $this->getJson('/api/me', $headers)->assertStatus(200);
        $this->getJson('/api/me', $headers)->assertStatus(200);

        $response = $this->getJson('/api/me', $headers);

        $response->assertStatus(429)
            ->assertJsonStructure(['message', 'limit', 'used', 'resets_at'])
            ->assertJsonPath('limit', 2)
            ->assertJsonPath('used', 2);

        $this->assertTrue(now('UTC')->lt($response->json('resets_at')));
    }

    // Test 3: admin nunca se limita, aunque supere el límite; no se escribe fila para él
    public function test_admin_is_never_limited_even_after_many_requests(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 1]);
        $admin = User::factory()->create(['role' => 'admin']);
        $headers = $this->authHeadersFor($admin);

        for ($i = 0; $i < 5; $i++) {
            $this->getJson('/api/me', $headers)->assertStatus(200);
        }

        $this->assertDatabaseMissing('api_usage_counters', [
            'subject_type' => 'user',
            'subject_id' => (string) $admin->id,
        ]);
    }

    // Test 4: usuario con override de límite custom respeta ese valor, no el default
    public function test_user_with_custom_override_respects_override_limit_not_default(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 2]);
        $user = User::factory()->create(['role' => 'user']);
        UserRateLimitOverride::create([
            'user_id' => $user->id,
            'daily_limit' => 5,
            'unlimited' => false,
        ]);
        $headers = $this->authHeadersFor($user);

        for ($i = 0; $i < 5; $i++) {
            $this->getJson('/api/me', $headers)->assertStatus(200);
        }

        $this->getJson('/api/me', $headers)
            ->assertStatus(429)
            ->assertJsonPath('limit', 5);
    }

    // Test 5: usuario con override unlimited nunca se bloquea
    public function test_user_with_unlimited_override_is_never_blocked(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 1]);
        $user = User::factory()->create(['role' => 'user']);
        UserRateLimitOverride::create([
            'user_id' => $user->id,
            'daily_limit' => null,
            'unlimited' => true,
        ]);
        $headers = $this->authHeadersFor($user);

        for ($i = 0; $i < 5; $i++) {
            $this->getJson('/api/me', $headers)->assertStatus(200);
        }
    }

    // Test 6: login cuenta para la cuota y bloquea con 429 tras el límite, aunque la contraseña sea incorrecta
    public function test_login_counts_toward_quota_and_blocks_after_limit_even_with_wrong_password(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 2]);
        $user = User::factory()->create(['role' => 'user']);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertStatus(500);
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertStatus(500);

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertStatus(429)
            ->assertJsonPath('limit', 2);

        $this->assertDatabaseHas('api_usage_counters', [
            'subject_type' => 'user',
            'subject_id' => (string) $user->id,
            'request_count' => 2,
        ]);
    }

    // Test 7: login con email inexistente nunca cuenta ni devuelve 429
    public function test_login_with_unknown_email_does_not_count_and_returns_normal_error(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 1]);

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/login', [
                'email' => 'no-existe@example.com',
                'password' => 'whatever1',
            ])->assertStatus(500);
        }

        $this->assertDatabaseCount('api_usage_counters', 0);
    }

    // Test 8: límite de registro por IP bloquea tras N peticiones, sin importar el usuario
    public function test_register_ip_limit_blocks_after_n_requests_regardless_of_user(): void
    {
        config(['rate_limiting.register_ip_daily_limit' => 2]);

        $payload = fn (string $email) => [
            'name' => 'Usuario de prueba largo',
            'email' => $email,
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $this->postJson('/api/register', $payload('user1@example.com'))->assertStatus(200);
        $this->postJson('/api/register', $payload('user2@example.com'))->assertStatus(200);
        $this->postJson('/api/register', $payload('user3@example.com'))
            ->assertStatus(429)
            ->assertJsonPath('limit', 2);

        $this->assertDatabaseHas('users', ['email' => 'user1@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'user2@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'user3@example.com']);
    }

    // Test 9: el contador reinicia en el cambio de día UTC
    public function test_counters_reset_at_midnight_utc_boundary(): void
    {
        config(['rate_limiting.default_daily_user_limit' => 1]);
        $user = User::factory()->create(['role' => 'user']);
        $headers = $this->authHeadersFor($user);

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-08-24 23:59:59', 'UTC'));
        $this->getJson('/api/me', $headers)->assertStatus(200);
        $this->getJson('/api/me', $headers)->assertStatus(429);

        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-08-25 00:00:01', 'UTC'));
        $this->getJson('/api/me', $headers)->assertStatus(200);

        $this->assertDatabaseHas('api_usage_counters', [
            'subject_type' => 'user',
            'subject_id' => (string) $user->id,
            'usage_date' => '2026-08-24',
            'request_count' => 1,
        ]);
        $this->assertDatabaseHas('api_usage_counters', [
            'subject_type' => 'user',
            'subject_id' => (string) $user->id,
            'usage_date' => '2026-08-25',
            'request_count' => 1,
        ]);
    }
}
