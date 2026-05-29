<?php

namespace App\DTO;

class UserDTO
{
    public function __construct(
        public ?int $id,
        public string $username,
        public string $email
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
        ];
    }
}
