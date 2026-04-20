<?php

namespace App\Models;

require_once __DIR__ . '/../../helpers/FinancialHelper.php';

class GenerateFinancial
{
    public static function generateRandomIban(): array
    {
        return generateValidSpanishIban();
    }

    public static function validateIban(string $iban): array
    {
        return validateSpanishIban($iban);
    }
}
