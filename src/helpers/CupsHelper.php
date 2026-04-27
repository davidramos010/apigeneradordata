<?php

/**
 * Tabla de letras de control para CUPS (índices 0–22).
 */
const CUPS_TABLA = 'TRWAGMYFPDXBNJZSQVHLCKE';

/**
 * Códigos de distribuidoras reales españolas por tipo de suministro.
 */
const CUPS_DISTRIBUIDORAS = [
    'electricidad' => ['0021', '0031', '0029', '0396', '0023'],
    'gas'          => ['0067', '0092', '0390', '0023'],
];

/**
 * Letras válidas para el tipo de frontera (posición 22 del CUPS).
 */
const CUPS_TIPOS_FRONTERA = ['F', 'P', 'R', 'M', 'C'];

/**
 * Calcula las dos letras de control a partir de los 16 dígitos centrales del CUPS.
 */
function _cupsCalcularLetrasControl(string $digitos16): string
{
    $numero = (int) $digitos16;
    $resto  = $numero % 529;
    $c      = (int) ($resto / 23);
    $r      = $resto % 23;

    return CUPS_TABLA[$c] . CUPS_TABLA[$r];
}

/**
 * Genera uno o varios CUPS españoles válidos.
 *
 * @param string $distribuidora Código de 4 dígitos de la distribuidora. Si vacío, se elige uno aleatorio según el tipo.
 * @param int    $cantidad      Número de CUPS a generar (1–100).
 * @param bool   $incluirSufijo Si true, añade dígito frontera + letra tipo al final (22 chars en total).
 * @param string $tipo          'electricidad' o 'gas'.
 * @return array{tipo: string, cups: string[]}
 */
function generateSpanishCups(string $distribuidora, int $cantidad, bool $incluirSufijo, string $tipo): array
{
    if ($distribuidora === '') {
        $opciones      = CUPS_DISTRIBUIDORAS[$tipo];
        $distribuidora = $opciones[array_rand($opciones)];
    }

    $cups = [];
    for ($i = 0; $i < $cantidad; $i++) {
        $suministro = str_pad((string) random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
        $digitos16  = $distribuidora . $suministro;
        $control    = _cupsCalcularLetrasControl($digitos16);
        $base       = 'ES' . $digitos16 . $control;

        if ($incluirSufijo) {
            $fronteraDigito = (string) random_int(0, 9);
            $fronteraTipo   = CUPS_TIPOS_FRONTERA[array_rand(CUPS_TIPOS_FRONTERA)];
            $base          .= $fronteraDigito . $fronteraTipo;
        }

        $cups[] = $base;
    }

    return ['tipo' => $tipo, 'cups' => $cups];
}

/**
 * Valida el formato y las letras de control de un CUPS español.
 *
 * @param string $cups El CUPS a validar.
 * @return array{valido: bool, cups?: string, detalles?: array<string, mixed>, errores: string[]}
 */
function validateSpanishCups(string $cups): array
{
    $cups    = strtoupper(trim($cups));
    $errores = [];
    $len     = strlen($cups);

    if ($len !== 20 && $len !== 22) {
        return ['valido' => false, 'errores' => ['Longitud incorrecta']];
    }

    if (substr($cups, 0, 2) !== 'ES') {
        $errores[] = 'El CUPS debe comenzar con ES';
    }

    $digitos16 = substr($cups, 2, 16);
    if (!ctype_digit($digitos16)) {
        $errores[] = 'Las posiciones 3-18 deben ser dígitos numéricos';
    }

    $controlRecibido = substr($cups, 18, 2);
    $letrasValidas   = true;
    if (!isset(array_flip(str_split(CUPS_TABLA))[$controlRecibido[0]]) ||
        !isset(array_flip(str_split(CUPS_TABLA))[$controlRecibido[1]])) {
        $errores[]    = 'Las letras de control no pertenecen a la tabla válida';
        $letrasValidas = false;
    }

    if (empty($errores) && $letrasValidas) {
        $controlEsperado = _cupsCalcularLetrasControl($digitos16);
        if ($controlRecibido !== $controlEsperado) {
            $errores[] = 'Los dígitos de control no coinciden';
        }
    }

    if ($len === 22) {
        $fronteraDigito = $cups[20];
        if (!ctype_digit($fronteraDigito)) {
            $errores[] = 'El designador de frontera (posición 21) debe ser un dígito';
        }

        $fronteraTipo = $cups[21];
        if (!in_array($fronteraTipo, CUPS_TIPOS_FRONTERA, true)) {
            $errores[] = 'El tipo de frontera (posición 22) debe ser F, P, R, M o C';
        }
    }

    if (!empty($errores)) {
        return ['valido' => false, 'errores' => $errores];
    }

    $controlEsperado = _cupsCalcularLetrasControl($digitos16);

    return [
        'valido'   => true,
        'cups'     => $cups,
        'detalles' => [
            'pais'             => 'ES',
            'distribuidora'    => substr($cups, 2, 4),
            'suministro'       => substr($cups, 6, 12),
            'controlEsperado'  => $controlEsperado,
            'controlRecibido'  => $controlRecibido,
            'sufijoFrontera'   => $len === 22 ? $cups[20] : null,
            'tipoFrontera'     => $len === 22 ? $cups[21] : null,
        ],
        'errores'  => [],
    ];
}
