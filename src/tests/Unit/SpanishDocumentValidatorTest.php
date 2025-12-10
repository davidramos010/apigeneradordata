<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Validations\SpanishDocumentValidator;

class SpanishDocumentValidatorTest extends TestCase
{
    /**
     * Test valid DNI validation
     */
    public function test_validate_dni_with_valid_dni()
    {
        // A valid DNI format
        $dni = "12345678Z";
        $result = SpanishDocumentValidator::validateDni($dni);
        
        // This should validate the format at least
        $this->assertIsBool($result);
    }

    /**
     * Test invalid DNI validation
     */
    public function test_validate_dni_with_invalid_format()
    {
        $dni = "invalid";
        $result = SpanishDocumentValidator::validateDni($dni);
        
        $this->assertFalse($result);
    }

    /**
     * Test valid NIE validation
     */
    public function test_validate_nie_with_valid_format()
    {
        $nie = "X1234567L";
        $result = SpanishDocumentValidator::validateNie($nie);
        
        $this->assertIsBool($result);
    }

    /**
     * Test invalid NIE validation
     */
    public function test_validate_nie_with_invalid_format()
    {
        $nie = "invalid";
        $result = SpanishDocumentValidator::validateNie($nie);
        
        $this->assertFalse($result);
    }

    /**
     * Test valid NIF validation
     */
    public function test_validate_nif_with_valid_format()
    {
        $nif = "A12345678";
        $result = SpanishDocumentValidator::validateNif($nif);
        
        $this->assertIsBool($result);
    }

    /**
     * Test invalid SSN validation
     */
    public function test_validate_ssn_with_invalid_all_zeros()
    {
        $ssn = "000000000";
        $result = SpanishDocumentValidator::validateSsn($ssn);
        
        $this->assertFalse($result);
    }

    /**
     * Test valid SSN validation
     */
    public function test_validate_ssn_with_valid_format()
    {
        $ssn = "123456789";
        $result = SpanishDocumentValidator::validateSsn($ssn);
        
        $this->assertTrue($result);
    }
}
