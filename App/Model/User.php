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

    public static function fromArray(array $data): self
    {
        return new self(
            $data['username'],
            $data['email'],
            $data['password'],
            isset($data['user_id']) ? (int) $data['user_id'] : null
        );
    }

    public static function toArray(self $user): array
    {
        return [
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email
        ];
    }
}
