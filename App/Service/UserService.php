<?php

namespace App\Service;

use App\Contract\UserInterface;
use App\Model\User;
use App\DTO\ApiResponse;

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
    ): ?User {

        $user = $this->repo
            ->findByUsername($usernameOrEmail);

        if ($user === null) {

            $user = $this->repo
                ->findByEmail($usernameOrEmail);
        }

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

        return $user;
    }

    public function register(array $data): ApiResponse
    {
        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = $data['password'];

        $errors = [];

        /**
         * BUSINESS VALIDATION
         */

        $existingUsername =
            $this->repo->findByUsername($username);

        if ($existingUsername !== null) {

            $errors['username'] =
                'Username is already taken.';
        }

        $existingEmail =
            $this->repo->findByEmail($email);

        if ($existingEmail !== null) {

            $errors['email'] =
                'Email is already registered.';
        }

        if (!empty($errors)) {

            return new ApiResponse(
                false,
                'Validation failed.',
                $errors
            );
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

        $user = $this->repo->read($userId);

        return new ApiResponse(
            true,
            'User registered successfully.',
            $user ? User::toArray($user) : null
        );
    }

    public function getByUsernameOrEmail(
        string $usernameOrEmail
    ): ?User {

        $user = $this->repo
            ->findByUsername($usernameOrEmail);

        if ($user === null) {

            $user = $this->repo
                ->findByEmail($usernameOrEmail);
        }

        return $user;
    }

    public function allUsersArray(): array
    {
        return array_map(
            fn(User $user) => User::toArray($user),
            $this->repo->getAll()
        );
    }
}
