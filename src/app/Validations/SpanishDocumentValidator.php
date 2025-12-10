<?php

namespace App\Validations;

/**
 * Class for validating Spanish documents
 */
class SpanishDocumentValidator
{
    /**
     * Control letters for Spanish documents
     */
    private const CONTROL_LETTERS = "TRWAGMYFPDXBNJZSQVHLCKE";

    /**
     * Validate DNI (Documento Nacional de Identidad)
     *
     * @param string $dni
     * @return bool
     */
    public static function validateDni(string $dni): bool
    {
        if (!preg_match('/^\d{8}[A-Z]$/', $dni)) {
            return false;
        }

        $number = substr($dni, 0, 8);
        $letter = substr($dni, 8, 1);
        
        $expectedLetter = self::CONTROL_LETTERS[intval($number) % 23];
        
        return $letter === $expectedLetter;
    }

    /**
     * Validate NIE (Número de Identidad de Extranjero)
     *
     * @param string $nie
     * @return bool
     */
    public static function validateNie(string $nie): bool
    {
        if (!preg_match('/^[XYZ]\d{7}[A-Z]$/', $nie)) {
            return false;
        }

        $prefix = substr($nie, 0, 1);
        $number = substr($nie, 1, 7);
        $letter = substr($nie, 8, 1);

        $numericPrefix = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $prefix);
        $expectedLetter = self::CONTROL_LETTERS[intval($numericPrefix . $number) % 23];

        return $letter === $expectedLetter;
    }

    /**
     * Validate NIF (Número de Identidad Fiscal)
     *
     * @param string $nif
     * @return bool
     */
    public static function validateNif(string $nif): bool
    {
        if (!preg_match('/^[A-Z]\d{7}[0-9A-Z]$/', $nif)) {
            return false;
        }

        $letter = substr($nif, 0, 1);
        $number = substr($nif, 1, 7);
        $control = substr($nif, 8, 1);

        $prefixValue = ord($letter) - ord('A');
        $fullNumber = $prefixValue . $number;

        $controlCharacters = "JABCDEFGHI";
        $expectedControl = $controlCharacters[intval($fullNumber) % 10];

        return $control === $expectedControl;
    }

    /**
     * Validate CIF (Código de Identificación Fiscal)
     *
     * @param string $cif
     * @return bool
     */
    public static function validateCif(string $cif): bool
    {
        if (!preg_match('/^[A-Z]\d{7}[0-9A-Z]$/', $cif)) {
            return false;
        }

        // CIF validation is more complex, simplified version here
        // In production, implement complete CIF validation algorithm
        return strlen($cif) === 9;
    }

    /**
     * Validate SSN (Social Security Number)
     *
     * @param string $ssn
     * @return bool
     */
    public static function validateSsn(string $ssn): bool
    {
        // Remove hyphens or spaces
        $ssn = preg_replace('/[-\s]/', '', $ssn);

        // Check format: XXX-XX-XXXX
        if (!preg_match('/^\d{9}$/', $ssn)) {
            return false;
        }

        // Basic validation: no all zeros, no 666 in first group
        if ($ssn === '000000000' || substr($ssn, 0, 3) === '666') {
            return false;
        }

        return true;
    }
}
