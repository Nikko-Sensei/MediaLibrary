<?php

namespace App\DTO;

class ApiResponse
{
    public function __construct(
        public bool $success,
        public string $message = '',
        public mixed $data = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
}
