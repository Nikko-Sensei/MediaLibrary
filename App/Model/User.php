<?php

namespace App\Model;

class User
{
    private ?int $id;
    private string $username;
    private string $email;
    private string $password;

    public function __construct(
        string $username,
        string $email,
        string $password,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
        ];
    }
}