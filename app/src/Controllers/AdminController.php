<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IAdminService;
use App\Services\IRoomService;
use App\Services\IUserService;

class AdminController
{
    private IAuthService $authService;
    private IAdminService $adminService;
    private IRoomService $roomService;
  
    //receives services and stores them for admin routes.
    public function __construct(
        IAuthService $authService,
        IAdminService $adminService,
        IRoomService $roomService
    ) {
        $this->authService = $authService;
        $this->adminService = $adminService;
        $this->roomService = $roomService;
    }

    //checks if the current user is an admin, redirects if not.
    private function requireAdmin(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $_SESSION['error_message'] = 'Please login first.';
            header('Location: /login');
            exit;
        }

        if (!$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'Access denied. Admin privileges required.';
            header('Location: /');
            exit;
        }
    }

    //returns the current logged-in user's ID from session/service
    private function currentUserId(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    // ============== DASHBOARD ===============
    //loads dashboard stats and shows the admin dashboard page
    public function dashboard(array $vars = []): void
    {
        $this->requireAdmin();
        
        $stats = $this->adminService->getDashboardStats();
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // =============== USER MANAGEMENT ==============
    //loads all users and shows the manage users page.
    public function users(array $vars = []): void
    {
        $this->requireAdmin();

        $users = $this->adminService->getAllUsers();
        require __DIR__ . '/../Views/admin/manageUsers.php';
    }

    //handles POST to change a user's role.
    public function updateUserRole(array $vars = []): void
    {
        $this->requireAdmin();

        $userId = (int)($vars['id'] ?? 0);
        $role = $_POST['role'] ?? 'player';

        $ok = $this->adminService->updateUserRole($userId, $role);
        $_SESSION[$ok ? 'success_message' : 'error_message'] = $ok ? 'User role updated.' : 'Could not update user role.';

        header('Location: /admin/users'); 
        exit;
    }

    // ==================== ROOM MANAGEMENT ==============
    //loads all rooms and shows the manage rooms page.
    public function rooms(array $vars = []): void
    {
        $this->requireAdmin();

        $rooms = $this->adminService->getAllRooms();
        require __DIR__ . '/../Views/admin/manageRooms.php'; 
    }

    //shows the “create room” form (admin only).
    public function createForm(array $vars = []): void
    {
        $this->requireAdmin();
        require __DIR__ . '/../Views/admin/room-create.php';
    }

    //handles POST to create a new room (admin only)
    public function createRoom(array $vars = []): void
    {
        $this->requireAdmin();
        
        try {
            $roomId = $this->roomService->createRoomFromPostData($_POST, $this->currentUserId());
            
            $_SESSION['success_message'] = 'Room created successfully!';
            header('Location: /admin/rooms'); 
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: /admin/rooms/create'); 
            exit;
        }
    }

    //shows the “edit room” form for a specific room (admin only).
    public function editRoomForm(array $vars = []): void
    {
        $this->requireAdmin();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /admin/rooms'); 
            exit;
        }

        require __DIR__ . '/../Views/admin/room-edit.php';
    }

    //handles POST to update a specific room (admin only).
    public function updateRoom(array $vars = []): void
    {
        $this->requireAdmin();
        
        $roomId = (int)($vars['id'] ?? 0);
        
        try {
            $this->roomService->updateRoomFromPostData($roomId, $_POST);
            
            $_SESSION['success_message'] = 'Room updated successfully!';
            header('Location: /admin/rooms'); 
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /admin/rooms/' . $roomId . '/edit'); 
            exit;
        }
    }

    //handles POST to delete a specific room (admin only).
    public function deleteRoom(array $vars = []): void
    {
        $this->requireAdmin();
        
        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);
        
        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /admin/rooms'); 
            exit;
        }
        
        $this->roomService->deleteRoom($roomId);
        
        $_SESSION['success_message'] = 'Room deleted successfully!';
        header('Location: /admin/rooms'); 
        exit;
    }

    //handles POST to publish/unpublish a specific room (admin only).
    public function toggleRoomPublish(array $vars = []): void
    {
        $this->requireAdmin();

        $roomId = (int)($vars['id'] ?? 0);
        $publish = isset($_POST['publish']) && $_POST['publish'] === '1';

        $ok = $this->adminService->toggleRoomPublish($roomId, $publish);
        $_SESSION[$ok ? 'success_message' : 'error_message'] = $ok ? 'Room status updated.' : 'Could not update room status.';

        header('Location: /admin/rooms'); 
        exit;
    }
}