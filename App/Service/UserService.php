<?php

namespace App\Service;

use App\Contract\UserInterface;
use App\Model\User;
use App\DTO\ApiResponse;
use App\DTO\RegisterDTO;
use App\DTO\UserDTO;
use App\DTO\UserMapper;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;


class UserService
{
    private UserInterface $repo;

    public function __construct(UserInterface $repo)
    {
        $this->repo = $repo;
    }

    public function authenticate(
        string $usernameOrEmail,
        string $password
    ): ?UserDTO {
        $user = $this->findUserByUsernameOrEmail($usernameOrEmail);

        if ($user === null) {
            return throw new NotFoundException('Uer not founds', []);
        }

        /**
         * VERIFY PASSWORD
         */

        if (!password_verify(
            $password,
            $user->getPassword()
        )) {
            return null;
        }

        return UserMapper::toDTO($user);
    }

    public function register(RegisterDTO|array $data): ApiResponse
    {
        $registerDTO = is_array($data)
            ? RegisterDTO::fromArray($data)
            : $data;

        $this->validateUniqueUser(
            $registerDTO->username,
            $registerDTO->email
        );

        /**
         * CREATE USER
         */

        $userId = $this->repo->create([

            'username' => $registerDTO->username,

            'email' => $registerDTO->email,

            'password' => password_hash(
                $registerDTO->password,
                PASSWORD_DEFAULT
            )
        ]);

        /**
         * FETCH CREATED USER
         */

        $user = $this->repo->read((int) $userId);

        if (!$user instanceof User) {
            return new ApiResponse(
                false,
                'Registration failed.',
                ['user' => 'Unable to load the created user.']
            );
        }

        return new ApiResponse(
            true,
            'Registration successful.',
            UserMapper::toDTO($user)
        );
    }

    public function getByUsernameOrEmail(
        string $usernameOrEmail
    ): ?UserDTO {
        $user = $this->findUserByUsernameOrEmail($usernameOrEmail);

        return $user instanceof User
            ? UserMapper::toDTO($user)
            : null;
    }

    private function validateUniqueUser(
        string $username,
        string $email
    ): void {
        $errors = [];

        if ($this->repo->findByUsername($username) !== null) {
            $errors['username'] = 'Username is already taken.';
        }

        if ($this->repo->findByEmail($email) !== null) {
            $errors['email'] = 'Email is already registered.';
        }

        if (empty($errors)) {
            throw new ValidationException('Validation failed.', $errors);
        }
    }

    private function findUserByUsernameOrEmail(
        string $usernameOrEmail
    ): ?User {

        $user = $this->repo
            ->findByUsername($usernameOrEmail);

        if ($user === null) {

            $user = $this->repo
                ->findByEmail($usernameOrEmail);
        }

        return $user instanceof User ? $user : null;
    }
}