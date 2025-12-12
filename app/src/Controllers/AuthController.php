<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\AuthService;
use App\Repositories\UserRepository;

class AuthController
{
    private IAuthService $authService;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $this->authService = new AuthService($userRepository);
    }

    public function showLogin(array $vars = []): void
    {
        // Redirect if already logged in
        if ($this->authService->isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function showRegister(array $vars = []): void
    {
        // Redirect if already logged in
        if ($this->authService->isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function login(array $vars = []): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->authService->login($email, $password);
        
        if ($user) {
            // Login successful
            $_SESSION['success_message'] = 'Welcome back, ' . $user->name . '!';
            header('Location: /');
            exit;
        } else {
            // Login failed
            $_SESSION['error_message'] = 'Invalid email or password.';
            header('Location: /login');
            exit;
        }
    }

    public function register(array $vars = []): void
    {
        try {
            // Validate password confirmation
            if ($_POST['password'] !== $_POST['confirm_password']) {
                throw new \InvalidArgumentException('Passwords do not match.');
            }
            
            $userId = $this->authService->register($_POST);
            
            // Auto-login after registration
            $this->authService->login($_POST['email'], $_POST['password']);
            
            $_SESSION['success_message'] = 'Registration successful! Welcome to RoomShift.';
            header('Location: /');
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: /register');
            exit;
        }
    }

    public function logout(array $vars = []): void
    {
        $this->authService->logout();
        
        $_SESSION['success_message'] = 'You have been logged out successfully.';
        header('Location: /');
        exit;
    }
}