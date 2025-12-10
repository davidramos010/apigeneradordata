<?php

namespace App\Contracts;

/**
 * Interface for document generation services
 */
interface DocumentGeneratorContract
{
    /**
     * Generate a document
     *
     * @return string
     */
    public function generate(): string;

    /**
     * Validate a document
     *
     * @param string $document
     * @return bool
     */
    public function validate(string $document): bool;
}
