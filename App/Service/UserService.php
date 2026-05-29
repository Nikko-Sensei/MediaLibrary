<?php

namespace App\Service;

use App\Contract\UserInterface;
use App\Model\User;
use App\DTO\UserDTO;
use App\DTO\UserMapper;
use App\DTO\ResponseDTO;


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
            return null;
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

    public function register(array $data): ResponseDTO
    {
        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = $data['password'];

        $validationResponse = $this->validateUniqueUser(
            $username,
            $email
        );

        if (!$validationResponse->success) {
            return $validationResponse;
        }



        /**
         * CREATE USER
         */

        $userId = $this->repo->create([

            'username' => $username,

            'email' => $email,

            'password' => password_hash(
                $password,
                PASSWORD_DEFAULT
            )
        ]);

        /**
         * FETCH CREATED USER
         */

        $user = $this->repo->read((int) $userId);

        if (!$user instanceof User) {
            return new ResponseDTO(
                false,
                'Registration failed.',
                ['user' => 'Unable to load the created user.']
            );
        }

        return new ResponseDTO(
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
    ): ResponseDTO {
        $errors = [];

        if ($this->repo->findByUsername($username) !== null) {
            $errors['username'] = 'Username is already taken.';
        }

        if ($this->repo->findByEmail($email) !== null) {
            $errors['email'] = 'Email is already registered.';
        }

        if (!empty($errors)) {
            return new ResponseDTO(
                false,
                'Validation failed.',
                $errors
            );
        }

        return new ResponseDTO(
            true,
            'User is valid.'
        );
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
