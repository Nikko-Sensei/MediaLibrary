<?php

namespace App\Controller;

use App\Exception\ValidationException;
use App\Request\LoginRequest;
use App\Request\RegisterUserRequest;
use App\Service\UserService;
use App\Validate\Validator;
use App\Controller\BaseController;
use App\Exception\NotFoundException;
use App\Exception\DatabaseException;

class AuthController extends BaseController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(
        LoginRequest $loginRequest,
        Validator $validator
    ): void {

        $pageTitle = 'Login';
        $section = 'login';
        $hideSearch = true;

        $usernameOrEmail = '';
        $errors = [];
        $errorMessage = null;
        $successMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $isValid = $validator->validate(
                $_POST,
                $loginRequest->rules()
            );

            if (!$isValid) {

                $errors = $validator->errors();
            } else {

                try {

                    $user = $this->userService->authenticate(
                        trim($_POST['username_or_email']),
                        $_POST['password']
                    );

                    $_SESSION['user'] = $user->toArray();

                    header(
                        'Location: ' .
                            BASE_URL .
                            '/Public/index.php?page=index'
                    );

                    exit;
                } catch (ValidationException $e) {
                    $errors = $e->errors();
                    $errorMessage = $e->getMessage();
                } catch (NotFoundException $e) {

                    $this->render404(
                        $e->getMessage()
                    );
                } catch (DatabaseException $e) {

                    $this->render500(
                        'Registration failed because of a database error.'
                    );
                } catch (\Throwable $e) {

                    $errorMessage =
                        'Unexpected error occurred.';
                }
            }
        }

        require BASE_PATH . '/view/login.php';
    }
    public function register(
        RegisterUserRequest $request,
        Validator $validator
    ): void {

        $pageTitle = 'Register';
        $section = 'register';
        $hideSearch = true;

        $username = '';
        $email = '';
        $successMessage = null;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username =
                trim($_POST['username'] ?? '');

            $email =
                trim($_POST['email'] ?? '');

            $isValid = $validator->validate(
                $_POST,
                $request->rules()
            );

            if (!$isValid) {

                $errors = $validator->errors();
            } else {

                try {

                    $response =
                        $this->userService->register([
                            'username' => $username,
                            'email' => $email,
                            'password' =>
                            $_POST['password'] ?? '',
                            'confirm_password' =>
                            $_POST['confirm_password'] ?? ''
                        ]);

                    $successMessage =
                        $response->message;

                    $username = '';
                    $email = '';
                } catch (ValidationException $e) {

                    $errors = $e->errors();
                } catch (NotFoundException $e) {

                    $this->render404(
                        $e->getMessage()
                    );
                } catch (DatabaseException $e) {

                    $this->render500(
                        'Registration failed because of a database error.'
                    );
                } catch (\Throwable $e) {

                    $this->render500(
                        'Unexpected error occurred.'
                    );
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
