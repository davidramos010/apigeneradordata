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

    public static function generateRandomCuenta(): array
    {
        return generateSpanishCuentaCorriente();
    }

    public static function generateRandomTarjeta(?string $type = null): array
    {
        return generateCreditCard($type);
    }
}
