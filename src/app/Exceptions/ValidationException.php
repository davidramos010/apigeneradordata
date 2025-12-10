<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception for validation errors
 */
class ValidationException extends Exception
{
    protected $code = 422;

    public function __construct(array $errors)
    {
        $this->message = json_encode($errors);
        parent::__construct($this->message, $this->code);
    }
}
