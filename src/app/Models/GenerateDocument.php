<?php

namespace App\Models;

// Incluir el archivo del helper
require_once __DIR__ . '/../../helpers/DocumentHelper.php';

class GenerateDocument
{
    public static function generateRandomDni()
    {
        // Usar la función del helper para generar un DNI válido
        return generateValidSpanishDni();
    }

    public static function generateRandomNie()
    {
        // Usar la función del helper para generar un NIE válido
        return generateValidSpanishNie();
    }

    public static function generateRandomCif()
    {
        // Usar la función del helper para generar un CIF válido
        return generateValidSpanishCif();
    }

    public static function generateRandomNif()
    {
        // Usar la función del helper para generar un NIF válido
        return generateValidSpanishNif();
    }

    public static function generateRandomSsn()
    {
        // Usar la función del helper para generar un NIES válido
        return generateValidSsn();
    }
}
