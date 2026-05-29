<?php

namespace App\Exception;

use Exception;

class NotFoundException extends Exception
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