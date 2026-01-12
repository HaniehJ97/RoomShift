<?php

namespace App\Controllers;

use App\Models\RoomModel;
use App\Services\IAuthService;
use App\Services\IRoomService;
use App\Services\IAdminService;

class RoomController
{
    private IAuthService $authService;
    private IRoomService $roomService;
    private IAdminService $adminService;

    public function __construct(
        IAuthService $authService,
        IRoomService $roomService,
        IAdminService $adminService
    ) {
        $this->authService = $authService;
        $this->roomService = $roomService;
        $this->adminService = $adminService;
    }

    // -----------------------------
    // Guards (unchanged)
    // -----------------------------
    private function requireLogin(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $_SESSION['error_message'] = 'Please login first.';
            header('Location: /login');
            exit;
        }
    }

    private function requireCreator(): void
    {
        $this->requireLogin();

        $user = $this->authService->getCurrentUser();
        if (!$user) {
            $_SESSION['error_message'] = 'Please login again.';
            header('Location: /login');
            exit;
        }

        if ($user->role !== 'creator' && $user->role !== 'admin') {
            $_SESSION['error_message'] = 'Access denied.';
            header('Location: /');
            exit;
        }
    }

    private function requireAdmin(): void
    {
        $this->requireLogin();

        if (!$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'Access denied.';
            header('Location: /');
            exit;
        }
    }

    private function currentUserId(): int
    {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    private function ensureCreatorOwnsRoom(RoomModel $room): void
    {
        $userId = $this->currentUserId();

        // admin can edit all
        if ($this->authService->isAdmin()) {
            return;
        }

        if ((int)$room->creator_id !== $userId) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }
    }

    // -----------------------------
    // Helper for reading POST data
    // -----------------------------
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

    // -----------------------------
    // PLAYER
    // -----------------------------
    public function index(array $vars = []): void
    {
        $isLoggedIn = $this->authService->isLoggedIn();
        $rooms = [];

        if ($isLoggedIn) {
            $user = $this->authService->getCurrentUser();
            $isCreatorOrAdmin = $user && ($user->role === 'creator' || $user->role === 'admin');

            if ($isCreatorOrAdmin) {
                $rooms = $this->roomService->getAllRooms();
            } else {
                $rooms = $this->roomService->getPublishedRooms();
            }
        } else {
            $rooms = $this->roomService->getPublishedRooms();
        }

        require __DIR__ . '/../Views/rooms/index.php';
    }

    public function play(array $vars = []): void
    {
        $this->requireLogin();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /rooms');
            exit;
        }

        // Pass room data to view (includes level config)
        require __DIR__ . '/../Views/rooms/play.php';
    }

    // -----------------------------
    // CREATOR (SIMPLIFIED!)
    // -----------------------------
    public function creatorRooms(array $vars = []): void
    {
        $this->requireCreator();

        $userId = $this->currentUserId();
        $rooms = $this->roomService->getRoomsByCreator($userId);

        require __DIR__ . '/../Views/creator/rooms.php';
    }

    public function createRoom(array $vars = []): void
    {
        $this->requireCreator();

        // ONE simple call - everything included!
        $roomData = $this->readRoomFromPost();
        $newRoomId = $this->roomService->createRoom($roomData);

        if ($newRoomId <= 0) {
            $_SESSION['error_message'] = 'Could not create room. Check your input.';
            $_SESSION['form_data'] = $_POST;
            header('Location: /creator/rooms');
            exit;
        }

        $_SESSION['success_message'] = 'Room created successfully!';
        header('Location: /creator/rooms');
        exit;
    }

    public function editRoomForm(array $vars = []): void
    {
        $this->requireCreator();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }

        $this->ensureCreatorOwnsRoom($room);

        require __DIR__ . '/../Views/creator/edit.php';
    }

    public function updateRoom(array $vars = []): void
    {
        $this->requireCreator();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }

        $this->ensureCreatorOwnsRoom($room);

        // Update from POST data
        $postData = $this->readRoomFromPost();
        
        $room->title = $postData['title'];
        $room->description = $postData['description'];
        $room->difficulty = $postData['difficulty'];
        $room->estimated_time = $postData['estimated_time'];
        $room->is_published = $postData['is_published'];
        $room->level_config = $postData['level_config'];

        $ok = $this->roomService->updateRoom($room);
        
        $_SESSION[$ok ? 'success_message' : 'error_message'] = 
            $ok ? 'Room updated successfully!' : 'Could not update room.';
        
        header('Location: /creator/rooms/' . $roomId . '/edit');
        exit;
    }

    public function deleteRoom(array $vars = []): void
    {
        $this->requireCreator();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }

        $this->ensureCreatorOwnsRoom($room);

        $ok = $this->roomService->deleteRoom($roomId);
        $_SESSION[$ok ? 'success_message' : 'error_message'] = $ok ? 'Room deleted.' : 'Could not delete room.';

        header('Location: /creator/rooms');
        exit;
    }

    // -----------------------------
    // ADMIN (unchanged)
    // -----------------------------
    public function adminDashboard(array $vars = []): void
    {
        $this->requireAdmin();

        $stats = $this->adminService->getDashboardStats();
        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function adminUsers(array $vars = []): void
    {
        $this->requireAdmin();

        $users = $this->adminService->getAllUsers();
        require __DIR__ . '/../Views/admin/users.php';
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

    public function adminRooms(array $vars = []): void
    {
        $this->requireAdmin();

        $rooms = $this->adminService->getAllRooms();
        require __DIR__ . '/../Views/admin/rooms.php';
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