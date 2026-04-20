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

    public static function generateRandomCifByType(string $strType)
    {
        // Usar la función del helper para generar un CIF válido según el tipo
        return generateValidSpanishCifByType($strType);
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

    public static function validateDni(string $dni): bool        { return validateSpanishDni($dni); }
    public static function validateNie(string $nie): bool        { return validateSpanishNie($nie); }
    public static function validateCif(string $cif): bool        { return validateSpanishCif($cif); }
    public static function validateNif(string $nif): bool        { return validateSpanishNif($nif); }
    public static function validateSsn(string $ssn): bool        { return validateSsn($ssn); }
    public static function validatePasaporte(string $p): bool    { return validatePasaporte($p); }
    public static function identifyDocumentType(string $doc): ?string { return identifyDocumentType($doc); }
}
