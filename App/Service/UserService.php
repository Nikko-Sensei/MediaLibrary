<?php

namespace App\Service;

use App\Contract\UserInterface;
use App\Model\User;
use App\DTO\ResponseDTO;
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
    ): UserDTO {

        $errors = [];

        // if (empty(trim($usernameOrEmail))) {
        //     $errors['username_or_email'] =
        //         'Username or Email is required.';
        // }

        // if (empty(trim($password))) {
        //     $errors['password'] =
        //         'Password is required.';
        // }

        // if (!empty($errors)) {
        //     throw new ValidationException(
        //         'Validation failed.',
        //         $errors
        //     );
        // }

        $user = $this->findUserByUsernameOrEmail(
            $usernameOrEmail
        );

        $invalidCredentials = !$user ||
            !password_verify(
                $password,
                $user->getPassword()
            );

        if ($invalidCredentials) {
            throw new ValidationException(
                'error_message',
                [
                    'error_message' =>
                    'Invalid username/email or password.'
                ]
            );
        }

        return UserMapper::toDTO($user);
    }

    public function register(array $data): ResponseDTO
    {
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        $errors = [];

        if ($this->repo->findByUsername($username) !== null) {

            $errors['username'] = 'Username is already taken.';
        }

        if ($this->repo->findByEmail($email) !== null) {
            $errors['email'] = 'Email is already registered.';
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed.', $errors);
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
