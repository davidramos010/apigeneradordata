<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateCupsTest extends TestCase
{
    use RefreshDatabase;

    private function authHeaders(): array
    {
        $user  = User::factory()->create(['role' => 'user']);
        $token = auth('api')->login($user);

        return ['Authorization' => "Bearer {$token}"];
    }

    // Test 1: POST /api/cups/generate con body válido retorna 200
    public function test_generate_cups_retorna_200_con_body_valido(): void
    {
        $response = $this->postJson('/api/cups/generate', [
            'tipo' => 'electricidad',
        ], $this->authHeaders());

        $response->assertStatus(200)
                 ->assertJsonStructure(['tipo', 'cups'])
                 ->assertJsonPath('tipo', 'electricidad');

        $this->assertCount(1, $response->json('cups'));
    }

    // Test 2: POST /api/cups/generate con cantidad=5 retorna 5 CUPS
    public function test_generate_cups_con_cantidad_5_retorna_5(): void
    {
        $response = $this->postJson('/api/cups/generate', [
            'tipo'     => 'electricidad',
            'cantidad' => 5,
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertCount(5, $response->json('cups'));
    }

    // Test 3: POST /api/cups/generate con distribuidora='0031' los CUPS la contienen
    public function test_generate_cups_con_distribuidora_especifica(): void
    {
        $response = $this->postJson('/api/cups/generate', [
            'tipo'          => 'electricidad',
            'distribuidora' => '0031',
            'cantidad'      => 3,
        ], $this->authHeaders());

        $response->assertStatus(200);
        foreach ($response->json('cups') as $cups) {
            $this->assertSame('0031', substr($cups, 2, 4));
        }
    }

    // Test 4: POST /api/cups/generate con tipo inválido retorna 400
    public function test_generate_cups_con_tipo_invalido_retorna_400(): void
    {
        $response = $this->postJson('/api/cups/generate', [
            'tipo' => 'agua',
        ], $this->authHeaders());

        $response->assertStatus(400)
                 ->assertJsonStructure(['message']);
    }

    // Test 5: POST /api/cups/generate sin autenticación retorna 401
    public function test_generate_cups_sin_auth_retorna_401(): void
    {
        $response = $this->postJson('/api/cups/generate', [
            'tipo' => 'electricidad',
        ]);

        $response->assertStatus(401);
    }

    // Test 6: POST /api/cups/validate con CUPS válido retorna valido=true
    public function test_validate_cups_valido_retorna_true(): void
    {
        // Usar round-trip: generar un CUPS y luego validarlo
        $generateResponse = $this->postJson('/api/cups/generate', [
            'tipo' => 'electricidad',
        ], $this->authHeaders());

        $cups = $generateResponse->json('cups')[0];

        $response = $this->postJson('/api/cups/validate', [
            'cups' => $cups,
        ], $this->authHeaders());

        $response->assertStatus(200)
                 ->assertJsonPath('valido', true)
                 ->assertJsonStructure(['valido', 'cups', 'detalles', 'errores']);
    }

    // Test 7: POST /api/cups/validate con CUPS inválido retorna valido=false
    public function test_validate_cups_invalido_retorna_false(): void
    {
        $response = $this->postJson('/api/cups/validate', [
            'cups' => 'ES0021000000000001AB', // letras de control incorrectas (deberían ser RK)
        ], $this->authHeaders());

        $response->assertStatus(200)
                 ->assertJsonPath('valido', false)
                 ->assertJsonStructure(['valido', 'errores']);
    }

    // Test 8: POST /api/cups/validate sin parámetro cups retorna 400
    public function test_validate_cups_sin_parametro_retorna_400(): void
    {
        $response = $this->postJson('/api/cups/validate', [], $this->authHeaders());

        $response->assertStatus(400)
                 ->assertJsonStructure(['message']);
    }
}
