<?php

/**
 * Valida un número de DNI español.
 *
 * @param string $dni
 * @return bool
 */
function validateSpanishDni(string $dni): bool
{
    $dni = strtoupper($dni);
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
        return false;
    }

    $number = substr($dni, 0, 8);
    $letter = substr($dni, 8, 1);
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";
    $expectedLetter = $controlLetters[$number % 23];

    return $letter === $expectedLetter;
}

/**
 * Valida un número de NIE español.
 *
 * @param string $nie
 * @return bool
 */
function validateSpanishNie(string $nie): bool
{
    $nie = strtoupper($nie);
    if (!preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $nie)) {
        return false;
    }

    $prefix = $nie[0];
    $number = substr($nie, 1, 7);
    $letter = substr($nie, 8, 1);

    $numericPrefix = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $prefix);
    $fullNumber = $numericPrefix . $number;

    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";
    $expectedLetter = $controlLetters[intval($fullNumber) % 23];

    return $letter === $expectedLetter;
}

/**
 * Valida un número de CIF español.
 *
 * @param string $cif
 * @return bool
 */
function validateSpanishCif(string $cif): bool
{
    $cif = strtoupper($cif);
    if (!preg_match('/^[A-HJ-NP-SUVW][0-9]{7}[0-9A-J]$/', $cif)) {
        return false;
    }

    $prefix = $cif[0];
    $number = substr($cif, 1, 7);
    $control = substr($cif, 8, 1);

    $sumEven = 0;
    $sumOdd = 0;

    for ($i = 0; $i < 7; $i++) {
        $digit = intval($number[$i]);
        if ($i % 2 === 0) { // Posiciones impares (1-based)
            $sumEven += $digit;
        } else { // Posiciones pares
            $double = $digit * 2;
            $sumOdd += ($double > 9) ? ($double - 9) : $double;
        }
    }

    $totalSum = $sumEven + $sumOdd;
    $controlDigit = (10 - ($totalSum % 10)) % 10;

    if (in_array($prefix, ['K', 'P', 'Q', 'S', 'W'])) {
        $expectedControl = chr(65 + $controlDigit); // Letra
    } else {
        $expectedControl = strval($controlDigit); // Dígito
    }

    return $control === $expectedControl;
}

/**
 * Valida un número de NIF español (DNI o NIE).
 *
 * @param string $nif
 * @return bool
 */
function validateSpanishNif(string $nif): bool
{
    $nif = strtoupper($nif);
    if (preg_match('/^[0-9]{8}[A-Z]$/', $nif)) {
        return validateSpanishDni($nif);
    }
    if (preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $nif)) {
        return validateSpanishNie($nif);
    }
    return false;
}

/**
 * Valida un número de SSN (Seguridad Social) español.
 *
 * @param string $ssn
 * @return bool
 */
function validateSsn(string $ssn): bool
{
    // Formato: 2 dígitos provincia + 8 dígitos número + 2 dígitos control
    // Esta es una validación simple de formato y checksum.
    if (!preg_match('/^[0-9]{12}$/', $ssn)) {
        return false;
    }
    $province = substr($ssn, 0, 2);
    $number = substr($ssn, 2, 8);
    $control = intval(substr($ssn, 10, 2));

    $numberToCheck = intval($province . $number);

    return ($numberToCheck % 97) === $control;
}