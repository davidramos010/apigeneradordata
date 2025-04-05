<?php

/**
 * Generate valid Spanish identification numbers (DNI, NIE, CIF).
 *
 * @return string
 */
function generateValidSpanishDni(): string
{
    // Array de letras de control para el DNI
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Generar un número aleatorio de 8 dígitos
    $dniNumber = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Calcular el índice de la letra de control
    $index = intval($dniNumber) % 23;

    // Obtener la letra de control correspondiente
    $controlLetter = $controlLetters[$index];

    // Retornar el DNI completo
    return $dniNumber . $controlLetter;
}

/**
   NIE (Número de Identidad de Extranjero):
   - Starts with `X`, `Y`, or `Z`.
   - The numeric equivalent of the prefix is used to calculate the control letter.
 */
function generateValidSpanishNie(): string
{
    // Array de letras de control para el NIE
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Generate a random starting letter (X, Y, or Z)
    $prefix = ['X', 'Y', 'Z'][rand(0, 2)];

    // Generate a random 7-digit number
    $number = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

    // Replace the prefix with its numeric equivalent for control letter calculation
    $numericPrefix = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $prefix);

    // Calculate the control letter
    $index = intval($numericPrefix . $number) % 23;
    $controlLetter = $controlLetters[$index];

    // Return the NIE
    return $prefix . $number . $controlLetter;
}

/**
 * **CIF**:
   - Starts with a letter indicating the type of entity.
   - The control character is calculated based on the sum of digits at odd and even positions.
   - The control character can be a digit or a letter depending on the prefix.
 *
 * @return string
 */
function generateValidSpanishCif(): string
{
    // Array of valid CIF starting letters
    $entityTypes = "ABCDEFGHJKLMNPQRSUVW";

    // Generate a random starting letter
    $prefix = $entityTypes[rand(0, strlen($entityTypes) - 1)];

    // Generate a random 7-digit number
    $number = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

    // Calculate the control character
    $sumEven = $sumOdd = 0;
    for ($i = 0; $i < 7; $i++) {
        $digit = intval($number[$i]);
        if ($i % 2 === 0) { // Odd positions (1-based index)
            $double = $digit * 2;
            $sumOdd += ($double > 9) ? $double - 9 : $double;
        } else { // Even positions
            $sumEven += $digit;
        }
    }
    $totalSum = $sumEven + $sumOdd;
    $controlDigit = (10 - ($totalSum % 10)) % 10;

    // Determine if the control character is a digit or letter
    $controlChar = ctype_alpha($prefix) && in_array($prefix, ['K', 'P', 'Q', 'S', 'W'])
        ? chr(65 + $controlDigit) // Convert to letter (A-J)
        : strval($controlDigit);  // Use digit

    // Return the CIF
    return $prefix . $number . $controlChar;
}

function generateValidSpanishNif(): string
{
    // Array de letras de control para el NIF
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Generar un número aleatorio de 8 dígitos
    $nifNumber = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Calcular el índice de la letra de control
    $index = intval($nifNumber) % 23;

    // Obtener la letra de control correspondiente
    $controlLetter = $controlLetters[$index];

    // Retornar el NIF completo
    return $nifNumber . $controlLetter;
}

function generateValidSsn(): string
{
    // Generar un número aleatorio de 8 dígitos
    $ssnNumber = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Retornar el SSN completo
    return $ssnNumber;
}
