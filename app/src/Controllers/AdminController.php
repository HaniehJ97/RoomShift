<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IAdminService;

class AdminController
{
    private IAuthService $authService;
    private IAdminService $adminService;

    public function __construct(IAuthService $authService,IAdminService $adminService) {
        
        $this->authService = $authService;
        $this->adminService = $adminService;
    }

    public function dashboard(array $vars = []): void
    {
        $this->checkAdminAccess();
        
        // Get data from SERVICES, not repositories
        $stats = $this->adminService->getDashboardStats();
        
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function listUsers(array $vars = []): void
    {
        $this->checkAdminAccess();
        
        // Get users via service
        $users = $this->adminService->getAllUsers();
        
        require __DIR__ . '/../Views/admin/users.php';
    }

    public function updateUserRole(array $vars = []): void
    {
        $this->checkAdminAccess();
        
        $userId = (int)($vars['id'] ?? 0);
        $role = $_POST['role'] ?? 'player';
        
        $success = $this->adminService->updateUserRole($userId, $role);
        
        if ($success) {
            $_SESSION['success_message'] = "User role updated successfully.";
        } else {
            $_SESSION['error_message'] = 'Failed to update user role.';
        }
        
        header('Location: /admin/users');
        exit;
    }

    public function listRooms(array $vars = []): void
    {
        $this->checkAdminAccess();
        
        $rooms = $this->adminService->getAllRooms();
        
        require __DIR__ . '/../Views/admin/rooms.php';
    }

    public function toggleRoomPublish(array $vars = []): void
    {
        $this->checkAdminAccess();
        
        $roomId = (int)($vars['id'] ?? 0);
        $publish = (bool)($_POST['publish'] ?? false);
        
        $success = $this->adminService->toggleRoomPublish($roomId, $publish);
        
        if ($success) {
            $_SESSION['success_message'] = 'Room status updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to update room status.';
        }
        
        header('Location: /admin/rooms');
        exit;
    }

    // ========== PRIVATE HELPER METHODS ==========
    
    private function checkAdminAccess(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $_SESSION['error_message'] = 'Please login to access admin panel.';
            header('Location: /login');
            exit;
        }
        
        if (!$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'Access denied. Admin privileges required.';
            header('Location: /');
            exit;
        }
    }
}