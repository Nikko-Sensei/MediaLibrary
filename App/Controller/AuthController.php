<?php

namespace App\Controller;

use App\Service\UserService;
use App\Request\LoginRequest;
use App\Request\RegisterUserRequest;
use App\Validate\Validator;

class AuthController
{
    private UserService $userService;
    // private Validator $validator;
    // private LoginRequest $loginRequest;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function login(LoginRequest $loginRequest, Validator $validator): void
    {
        $pageTitle = 'Login';
        $section = 'login';
        $hideSearch = true;

        $usernameOrEmail = '';
        $errors = [];
        $errorMessage = null;
        $successMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
            $password = $_POST['password'] ?? '';

            // validate
            // $request = new LoginRequest();
            // $validator = new Validator();

            $isValid = $validator->validate($_POST, $loginRequest->rules());

            if (!$isValid) {
                $errors = $validator->errors();

                require BASE_PATH . '/view/login.php';
                return; // 🔥 IMPORTANT FIX
            }

            $user = $this->userService->authenticate($usernameOrEmail, $password);

            if ($user === null) {
                $errors['error_message'] = 'Invalid login credentials.';

                require BASE_PATH . '/view/login.php';
                return;
            }

            // success
            $_SESSION['user'] = \App\Model\User::toArray($user);
            $_SESSION['success_message'] = 'Login successful!';

            header(
                'Location: ' . BASE_URL .
                    '/Public/index.php?page=index'
            );
            exit;
        }

        require BASE_PATH . '/view/login.php';
    }

    public function register(RegisterUserRequest $request, Validator $validator): void
    {
        $pageTitle = 'Register';
        $section = 'register';
        $hideSearch = true;

        $username = '';
        $email = '';
        $successMessage = null;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');

            $isValid = $validator->validate(
                $_POST,
                $request->rules()
            );

            if (!$isValid) {

                $errors = $validator->errors();
            } else {

                $response = $this->userService->register([

                    'username' => $username,

                    'email' => $email,

                    'password' => $_POST['password'] ?? '',

                    'confirm_password' =>
                    $_POST['confirm_password'] ?? ''
                ]);

                if ($response->success) {

                    $successMessage =
                        $response->message;

                    $username = '';
                    $email = '';
                } else {

                    $errors = $response->data ?? [];
                }
            }
        }

        require BASE_PATH . '/view/register.php';
    }

    public function logout(): void
    {
        session_unset();

        session_destroy();

        header(
            'Location: '
                . BASE_URL
                . '/Public/index.php?page=index'
        );

        exit;
    }
}
