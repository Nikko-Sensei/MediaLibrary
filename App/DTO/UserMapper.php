<?php

namespace App\DTO;

use App\Model\User;

class UserMapper
{
    public static function toDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getId(),
            $user->getUsername(),
            $user->getEmail()
        );
    }

    public static function toArray(UserDTO $dto): array
    {
        return $dto->toArray();
    }
}
