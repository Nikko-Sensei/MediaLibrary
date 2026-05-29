<?php

namespace App\Exception;

use RuntimeException;
use Exception;

class ValidationException extends Exception
{
    public function __construct(
        string $message = '',
        private array $errors = []
    ) {
        parent::__construct($message);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}