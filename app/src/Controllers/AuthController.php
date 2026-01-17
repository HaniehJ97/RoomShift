<?php

namespace App\Controllers;

use App\Services\IAuthService;

class AuthController
{
    private IAuthService $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }

    //renders the login form
    public function showLogin(): void
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    //renders the registration form
    public function showRegister(): void
    {
        require __DIR__ . '/../Views/auth/register.php';
    }

    //handles POST to login form, uses AuthService to login and redirects with messages
    public function login(array $vars = []): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->authService->login($email, $password);

        if ($user === null) {
            $_SESSION['error_message'] = 'Invalid email or password.';
            header('Location: /rooms');
            exit;
        }

        $_SESSION['success_message'] = 'Welcome back, ' . $user->name . '!';
        header('Location: /');
        exit;
    }

    //handles POST to registration form, uses AuthService to register and redirects with messages
    public function register(array $vars = []): void
    {
        try {
            $this->authService->register($_POST);

            $_SESSION['success_message'] = 'Account created successfully.';
            header('Location: /');
            exit;
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;

            header('Location: /register');
            exit;
        }
    }

    //logs out the user and redirects to login page
    public function logout(): void
    {
        $this->authService->logout();

        header('Location: /login');
        exit;
    }
}