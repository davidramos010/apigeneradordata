<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../helpers/CupsHelper.php';

class CupsHelperTest extends TestCase
{
    // CUPS conocido-válido de 20 chars (calculado: 0021000000000001 → mod 529 = 44 → c=1,r=21 → RK)
    private const CUPS_20_VALIDO = 'ES0021000000000001RK';

    // Mismo CUPS con sufijo de frontera válido
    private const CUPS_22_VALIDO = 'ES0021000000000001RK1F';

    // Test 1: CUPS de 20 chars conocido → válido
    public function test_cups_20_chars_valido_pasa_validacion(): void
    {
        $resultado = validateSpanishCups(self::CUPS_20_VALIDO);

        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['errores']);
        $this->assertSame('ES', $resultado['detalles']['pais']);
        $this->assertSame('0021', $resultado['detalles']['distribuidora']);
        $this->assertSame('000000000001', $resultado['detalles']['suministro']);
        $this->assertNull($resultado['detalles']['sufijoFrontera']);
        $this->assertNull($resultado['detalles']['tipoFrontera']);
    }

    // Test 2: CUPS de 22 chars con sufijo válido → válido
    public function test_cups_22_chars_con_sufijo_valido_pasa_validacion(): void
    {
        $resultado = validateSpanishCups(self::CUPS_22_VALIDO);

        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['errores']);
        $this->assertSame('1', $resultado['detalles']['sufijoFrontera']);
        $this->assertSame('F', $resultado['detalles']['tipoFrontera']);
    }

    // Test 3: CUPS con longitud < 20 → error de longitud
    public function test_cups_demasiado_corto_retorna_error_longitud(): void
    {
        $resultado = validateSpanishCups('ES0021000000000001');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('Longitud incorrecta', $resultado['errores']);
    }

    // Test 4: CUPS con longitud > 22 → error de longitud
    public function test_cups_demasiado_largo_retorna_error_longitud(): void
    {
        $resultado = validateSpanishCups('ES0021000000000001RK1FX');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('Longitud incorrecta', $resultado['errores']);
    }

    // Test 5: CUPS que no empieza con ES → error de prefijo
    public function test_cups_con_prefijo_incorrecto_falla(): void
    {
        $resultado = validateSpanishCups('XX0021000000000001RK');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('El CUPS debe comenzar con ES', $resultado['errores']);
    }

    // Test 6: CUPS con suministro no numérico → error de dígitos
    public function test_cups_con_suministro_no_numerico_falla(): void
    {
        $resultado = validateSpanishCups('ES0021ABCDEF000001RK');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('Las posiciones 3-18 deben ser dígitos numéricos', $resultado['errores']);
    }

    // Test 7: CUPS con letras de control incorrectas → error de control
    public function test_cups_con_letras_control_incorrectas_falla(): void
    {
        // ES0021000000000001AB — las letras correctas son RK, no AB
        $resultado = validateSpanishCups('ES0021000000000001AB');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('Los dígitos de control no coinciden', $resultado['errores']);
    }

    // Test 8: CUPS de 22 chars con letra en lugar de dígito en pos 21 → error
    public function test_cups_22_con_letra_en_posicion_frontera_falla(): void
    {
        $resultado = validateSpanishCups('ES0021000000000001RKXF');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('El designador de frontera (posición 21) debe ser un dígito', $resultado['errores']);
    }

    // Test 9: CUPS de 22 chars con tipo frontera inválido (X no está en F,P,R,M,C) → error
    public function test_cups_22_con_tipo_frontera_invalido_falla(): void
    {
        $resultado = validateSpanishCups('ES0021000000000001RK1X');

        $this->assertFalse($resultado['valido']);
        $this->assertContains('El tipo de frontera (posición 22) debe ser F, P, R, M o C', $resultado['errores']);
    }

    // Test 10: generateSpanishCups con cantidad=5 devuelve array de 5 elementos
    public function test_generate_cups_con_cantidad_5_retorna_5_elementos(): void
    {
        $resultado = generateSpanishCups('0021', 5, false, 'electricidad');

        $this->assertCount(5, $resultado['cups']);
        $this->assertSame('electricidad', $resultado['tipo']);
    }

    // Test 11: CUPS generado con distribuidora específica la contiene en las posiciones correctas
    public function test_generate_cups_con_distribuidora_especifica_la_incluye(): void
    {
        $resultado = generateSpanishCups('0031', 3, false, 'electricidad');

        foreach ($resultado['cups'] as $cups) {
            $this->assertSame('0031', substr($cups, 2, 4));
        }
    }

    // Test 12: Round-trip: CUPS generado pasa la validación
    public function test_cups_generado_pasa_validacion(): void
    {
        $generado  = generateSpanishCups('0021', 1, false, 'electricidad');
        $cups      = $generado['cups'][0];
        $resultado = validateSpanishCups($cups);

        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['errores']);
    }

    // Test 13: incluirSufijo=true produce CUPS de 22 chars
    public function test_generate_con_sufijo_produce_cups_de_22_chars(): void
    {
        $resultado = generateSpanishCups('0021', 5, true, 'gas');

        foreach ($resultado['cups'] as $cups) {
            $this->assertSame(22, strlen($cups));
        }
    }

    // Test 14: incluirSufijo=false produce CUPS de 20 chars
    public function test_generate_sin_sufijo_produce_cups_de_20_chars(): void
    {
        $resultado = generateSpanishCups('0021', 5, false, 'electricidad');

        foreach ($resultado['cups'] as $cups) {
            $this->assertSame(20, strlen($cups));
        }
    }
}
