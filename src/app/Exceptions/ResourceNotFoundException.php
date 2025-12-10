<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception for resource not found
 */
class ResourceNotFoundException extends Exception
{
    protected $message = 'Resource not found';
    protected $code = 404;
}
