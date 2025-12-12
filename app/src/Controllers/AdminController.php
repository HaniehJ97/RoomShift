<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\AuthService;
use App\Repositories\UserRepository;

class AdminController
{
    private IAuthService $authService;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $this->authService = new AuthService($userRepository);
    }

    public function dashboard(array $vars = []): void
    {
        // Check if user is logged in and is admin
        if (!$this->authService->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        
        if (!$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'Access denied. Admin privileges required.';
            header('Location: /');
            exit;
        }
        
        // Get current user
        $user = $this->authService->getCurrentUser();
        
        require __DIR__ . '/../Views/admin/dashboard.php';
    }
}