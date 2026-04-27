<?php

namespace App\Models;

require_once __DIR__ . '/../../helpers/CupsHelper.php';

class GenerateCups
{
    public static function generateCups(string $distribuidora, int $cantidad, bool $incluirSufijo, string $tipo): array
    {
        return generateSpanishCups($distribuidora, $cantidad, $incluirSufijo, $tipo);
    }

    public static function validateCups(string $cups): array
    {
        return validateSpanishCups($cups);
    }
}
