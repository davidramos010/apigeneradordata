<?php

namespace App\Services;

use App\Contracts\DocumentGeneratorContract;
use App\Models\GenerateDocument;
use App\Validations\SpanishDocumentValidator;

/**
 * Service for generating and validating documents
 */
class DocumentGeneratorService
{
    /**
     * Generate a DNI (Documento Nacional de Identidad)
     *
     * @return string
     */
    public function generateDni(): string
    {
        return GenerateDocument::generateRandomDni();
    }

    /**
     * Validate a DNI
     *
     * @param string $dni
     * @return bool
     */
    public function validateDni(string $dni): bool
    {
        return SpanishDocumentValidator::validateDni($dni);
    }

    /**
     * Generate a NIE (Número de Identidad de Extranjero)
     *
     * @return string
     */
    public function generateNie(): string
    {
        return GenerateDocument::generateRandomNie();
    }

    /**
     * Validate a NIE
     *
     * @param string $nie
     * @return bool
     */
    public function validateNie(string $nie): bool
    {
        return SpanishDocumentValidator::validateNie($nie);
    }

    /**
     * Generate a NIF (Número de Identidad Fiscal)
     *
     * @return string
     */
    public function generateNif(): string
    {
        return GenerateDocument::generateRandomNif();
    }

    /**
     * Validate a NIF
     *
     * @param string $nif
     * @return bool
     */
    public function validateNif(string $nif): bool
    {
        return SpanishDocumentValidator::validateNif($nif);
    }

    /**
     * Generate a CIF (Código de Identificación Fiscal)
     *
     * @return string
     */
    public function generateCif(): string
    {
        return GenerateDocument::generateRandomCif();
    }

    /**
     * Validate a CIF
     *
     * @param string $cif
     * @return bool
     */
    public function validateCif(string $cif): bool
    {
        return SpanishDocumentValidator::validateCif($cif);
    }

    /**
     * Generate a SSN (Social Security Number)
     *
     * @return string
     */
    public function generateSsn(): string
    {
        return GenerateDocument::generateRandomSsn();
    }

    /**
     * Validate a SSN
     *
     * @param string $ssn
     * @return bool
     */
    public function validateSsn(string $ssn): bool
    {
        return SpanishDocumentValidator::validateSsn($ssn);
    }
}
