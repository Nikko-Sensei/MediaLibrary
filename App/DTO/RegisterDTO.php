<?php

namespace App\DTO;

class RegisterDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            trim($data['username'] ?? ''),
            trim($data['email'] ?? ''),
            $data['password'] ?? ''
        );
    }
}
