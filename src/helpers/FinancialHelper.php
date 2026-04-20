<?php

/**
 * Generates a valid Spanish IBAN.
 *
 * Structure (24 chars): ES + 2 check digits (MOD-97) + BBAN(20)
 * BBAN = bank(4) + branch(4) + DC1(1) + DC2(1) + account(10)
 *
 * @return array{iban: string, formatted: string, components: array}
 */
function generateValidSpanishIban(): array
{
    $bankCode   = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $branchCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $account    = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

    $dc1 = ibanCccControlDigit('0' . $bankCode . $branchCode, [4, 8, 5, 10, 9, 7, 3, 6, 1]);
    $dc2 = ibanCccControlDigit($account, [1, 2, 4, 8, 5, 10, 9, 7, 3, 6]);

    $controlDigits = $dc1 . $dc2;
    $bban          = $bankCode . $branchCode . $controlDigits . $account;

    // ISO 13616: append "ES00", replace letters (E=14, S=28), compute MOD-97
    $checkDigits = str_pad(98 - ibanMod97($bban . '142800'), 2, '0', STR_PAD_LEFT);
    $iban        = 'ES' . $checkDigits . $bban;

    return [
        'iban'       => $iban,
        'formatted'  => implode(' ', str_split($iban, 4)),
        'components' => [
            'country'        => 'ES',
            'check_digits'   => $checkDigits,
            'bank_code'      => $bankCode,
            'branch_code'    => $branchCode,
            'control_digits' => $controlDigits,
            'account_number' => $account,
        ],
    ];
}

/**
 * Validates a Spanish IBAN.
 *
 * Checks: correct length (24), ES prefix, valid MOD-97 checksum, and valid CCC control digits.
 *
 * @return array{iban: string, valid: bool, message: string, components: array|null}
 */
function validateSpanishIban(string $input): array
{
    $iban = strtoupper(preg_replace('/\s+/', '', $input));

    if (strlen($iban) !== 24) {
        return _ibanResult($input, false, 'El IBAN debe tener exactamente 24 caracteres.');
    }

    if (!str_starts_with($iban, 'ES')) {
        return _ibanResult($input, false, 'El IBAN español debe comenzar con "ES".');
    }

    if (!preg_match('/^ES\d{22}$/', $iban)) {
        return _ibanResult($input, false, 'El IBAN contiene caracteres no válidos.');
    }

    // MOD-97 validation: move first 4 chars (ESCC) to end → BBAN + "1428" + CC
    $rearranged = substr($iban, 4) . '1428' . substr($iban, 2, 2);
    if (ibanMod97($rearranged) !== 1) {
        return _ibanResult($input, false, 'Los dígitos de control IBAN no son válidos (falla MOD-97).');
    }

    // CCC control digit validation
    $bankCode   = substr($iban, 4, 4);
    $branchCode = substr($iban, 8, 4);
    $dc         = substr($iban, 12, 2);
    $account    = substr($iban, 14, 10);

    $expectedDc1 = ibanCccControlDigit('0' . $bankCode . $branchCode, [4, 8, 5, 10, 9, 7, 3, 6, 1]);
    $expectedDc2 = ibanCccControlDigit($account, [1, 2, 4, 8, 5, 10, 9, 7, 3, 6]);

    if ($dc !== ($expectedDc1 . $expectedDc2)) {
        return _ibanResult($input, false, 'Los dígitos de control CCC no son válidos.');
    }

    return [
        'iban'       => $iban,
        'formatted'  => implode(' ', str_split($iban, 4)),
        'valid'      => true,
        'message'    => 'VALIDO',
        'components' => [
            'country'        => 'ES',
            'check_digits'   => substr($iban, 2, 2),
            'bank_code'      => $bankCode,
            'branch_code'    => $branchCode,
            'control_digits' => $dc,
            'account_number' => $account,
        ],
    ];
}

function ibanCccControlDigit(string $digits, array $weights): int
{
    $sum = 0;
    for ($i = 0; $i < strlen($digits); $i++) {
        $sum += intval($digits[$i]) * $weights[$i];
    }
    $result = 11 - ($sum % 11);
    if ($result === 11) return 0;
    if ($result === 10) return 1;
    return $result;
}

function ibanMod97(string $number): int
{
    $remainder = 0;
    foreach (str_split($number) as $digit) {
        $remainder = ($remainder * 10 + intval($digit)) % 97;
    }
    return $remainder;
}

function _ibanResult(string $input, bool $valid, string $message): array
{
    return [
        'iban'       => strtoupper(preg_replace('/\s+/', '', $input)),
        'formatted'  => null,
        'valid'      => $valid,
        'message'    => $message,
        'components' => null,
    ];
}
