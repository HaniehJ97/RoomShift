<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IAdminService;
use App\Services\IRoomService;

class AdminController
{
    private IAuthService $authService;
    private IAdminService $adminService;
    private IRoomService $roomService;

    public function __construct(
        IAuthService $authService,
        IAdminService $adminService,
        IRoomService $roomService
    ) {
        $this->authService = $authService;
        $this->adminService = $adminService;
        $this->roomService = $roomService;
    }

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

    private function currentUserId(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    private function readRoomFromPost(): array
    {
        // Get the JSON config from the form
        $configJson = $_POST['config_json'] ?? '{}';
        $config = json_decode($configJson, true) ?? [];
        
        // Ensure all required fields exist
        $defaultConfig = [
            'grid_width' => 12,
            'grid_height' => 12,
            'walls' => [],
            'bombs' => [],
            'key' => ['x' => 0, 'y' => 0],
            'door' => ['x' => 11, 'y' => 11]
        ];
        
        $levelConfig = array_merge($defaultConfig, $config);
        
        // Get grid dimensions (they might also be in separate fields)
        $levelConfig['grid_width'] = (int)($_POST['grid_width'] ?? $levelConfig['grid_width']);
        $levelConfig['grid_height'] = (int)($_POST['grid_height'] ?? $levelConfig['grid_height']);
        
        return [
            'title'         => $_POST['title'] ?? '',
            'description'   => $_POST['description'] ?? '',
            'creator_id'    => $this->currentUserId(),
            'difficulty'    => $_POST['difficulty'] ?? 'easy',
            'estimated_time'=> (int)($_POST['estimated_time'] ?? 30),
            'is_published'  => isset($_POST['is_published']) && $_POST['is_published'] === '1',
            'level_config'  => $levelConfig
        ];
    }

    // ==================== DASHBOARD ====================
    public function dashboard(array $vars = []): void
    {
        $this->requireAdmin();
        
        $stats = $this->adminService->getDashboardStats();
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    // ==================== USER MANAGEMENT ====================
    public function users(array $vars = []): void
    {
        $this->requireAdmin();

        $users = $this->adminService->getAllUsers();
        require __DIR__ . '/../Views/admin/manageUsers.php';
    }

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

    // ==================== ROOM MANAGEMENT ====================
    public function rooms(array $vars = []): void
    {
        $this->requireAdmin();

        $rooms = $this->adminService->getAllRooms();
        require __DIR__ . '/../Views/admin/manageRooms.php'; 
    }

    public function createForm(array $vars = []): void
    {
        $this->requireAdmin();
        require __DIR__ . '/../Views/admin/room-create.php';
    }

    public function createRoom(array $vars = []): void
    {
        $this->requireAdmin();
        
        try {
            $roomData = $this->readRoomFromPost();
            
            $roomId = $this->roomService->createRoom($roomData);
            
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

    public function updateRoom(array $vars = []): void
    {
        $this->requireAdmin();
        
        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);
        
        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /admin/rooms'); 
            exit;
        }
        
        try {
            // Update room properties
            $room->title = $_POST['title'] ?? $room->title;
            $room->description = $_POST['description'] ?? $room->description;
            $room->difficulty = $_POST['difficulty'] ?? $room->difficulty;
            $room->estimated_time = (int)($_POST['estimated_time'] ?? $room->estimated_time);
            $room->is_published = isset($_POST['is_published']) && $_POST['is_published'] === '1';
            
            // Update level config if provided
            if (!empty($_POST['config_json'])) {
                $config = json_decode($_POST['config_json'], true);
                if ($config) {
                    $room->level_config = array_merge($room->level_config, $config);
                }
            }
            
            $this->roomService->updateRoom($room);
            
            $_SESSION['success_message'] = 'Room updated successfully!';
            header('Location: /admin/rooms'); 
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /admin/rooms/' . $roomId . '/edit'); 
            exit;
        }
    }

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