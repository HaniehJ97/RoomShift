<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IRoomService;
use App\Services\IAdminService;
use App\Services\IRoomLevelService;

class RoomController
{
    private IAuthService $authService;
    private IRoomService $roomService;
    private IAdminService $adminService;
    private IRoomLevelService $roomLevelService;

    public function __construct(
    IAuthService $authService,
    IRoomService $roomService,
    IAdminService $adminService,
    IRoomLevelService $roomLevelService
) {
    $this->authService = $authService;
    $this->roomService = $roomService;
    $this->adminService = $adminService;
    $this->roomLevelService = $roomLevelService;
}

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
        if (!$user || ($user->role !== 'creator' && $user->role !== 'admin')) {
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

    // -----------------------------
    // PLAYER (basic user)
    // -----------------------------
    public function index(array $vars = []): void
    {
        $isLoggedIn = $this->authService->isLoggedIn();
        $isCreator = false;
        $isAdmin = false;

        if ($isLoggedIn) {
            $user = $this->authService->getCurrentUser();
            $isCreator = $user && ($user->role === 'creator' || $user->role === 'admin');
            $isAdmin = $user && $user->role === 'admin';
        }

        if ($isCreator || $isAdmin) {
            $rooms = $this->roomService->getAllRooms();
        } else {
            $rooms = $this->roomService->getPublishedRooms();
        }

        require __DIR__ . '/../Views/rooms/index.php';
    }

    // placeholder for later when you start the game screen
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
        $level = $this->roomLevelService->getByRoomId($roomId);

        if (!$level) {
            $level = [
                'grid_width' => 12,
                'grid_height' => 12,
                'difficulty' => 'easy',
                'config_json' => '{"walls":[],"bombs":[],"foods":[]}'
            ];
        }

        require __DIR__ . '/../Views/rooms/play.php';
    }

    // -----------------------------
    // CREATOR
    // -----------------------------
    public function creatorRooms(array $vars = []): void
    {
        $this->requireCreator();

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $rooms = $this->roomService->getRoomsByCreator($userId);

        require __DIR__ . '/../Views/creator/rooms.php';
    }

    public function createRoom(array $vars = []): void
    {
        $this->requireCreator();

        try {
            $roomData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'creator_id' => (int)($_SESSION['user_id'] ?? 0),
                'difficulty' => $_POST['difficulty'] ?? 'medium',
                'estimated_time' => (int)($_POST['estimated_time'] ?? 30),
                'is_published' => isset($_POST['is_published']) && $_POST['is_published'] === '1'
            ];

            $this->roomService->createRoom($roomData);

            $_SESSION['success_message'] = 'Room created successfully!';
            header('Location: /creator/rooms');
            exit;

        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: /creator/rooms');
            exit;
        }
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

        if ($room->creator_id !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }

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

        if ($room->creator_id !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }

        try {
            $room->title = $_POST['title'] ?? $room->title;
            $room->description = $_POST['description'] ?? $room->description;
            $room->difficulty = $_POST['difficulty'] ?? $room->difficulty;
            $room->estimated_time = (int)($_POST['estimated_time'] ?? $room->estimated_time);
            $room->is_published = isset($_POST['is_published']) && $_POST['is_published'] === '1';

            $this->roomService->updateRoom($room);

            $_SESSION['success_message'] = 'Room updated successfully!';
            header('Location: /creator/rooms');
            exit;

        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /creator/rooms/' . $roomId . '/edit');
            exit;
        }
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

        if ($room->creator_id !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'You can only delete your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }

        $this->roomService->deleteRoom($roomId);

        $_SESSION['success_message'] = 'Room deleted successfully!';
        header('Location: /creator/rooms');
        exit;
    }

    // -----------------------------
    // ADMIN
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

        $success = $this->adminService->updateUserRole($userId, $role);

        $_SESSION[$success ? 'success_message' : 'error_message'] =
            $success ? 'User role updated successfully.' : 'Failed to update user role.';

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
        $publish = (bool)($_POST['publish'] ?? false);

        $success = $this->adminService->toggleRoomPublish($roomId, $publish);

        $_SESSION[$success ? 'success_message' : 'error_message'] =
            $success ? 'Room status updated successfully.' : 'Failed to update room status.';

        header('Location: /admin/rooms');
        exit;
    }
    // -----------------------------
    // LEVELUP
    // -----------------------------
        public function levelEditor(array $vars = []): void
    {
        $this->requireCreator();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }

        if ($room->creator_id !== (int)($_SESSION['user_id'] ?? 0) && !$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }

        $level = $this->roomLevelService->getByRoomId($roomId);

        require __DIR__ . '/../Views/creator/level.php';
    }

    public function saveLevel(array $vars = []): void
    {
        $this->requireCreator();

        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }

        if ($room->creator_id !== (int)($_SESSION['user_id'] ?? 0) && !$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }

        $gridW = (int)($_POST['grid_width'] ?? 12);
        $gridH = (int)($_POST['grid_height'] ?? 12);
        $difficulty = $_POST['difficulty'] ?? 'easy';
        $configJson = $_POST['config_json'] ?? '{"walls":[],"bombs":[],"foods":[]}';

        $ok = $this->roomLevelService->saveLevel($roomId, $gridW, $gridH, $difficulty, $configJson);

        $_SESSION[$ok ? 'success_message' : 'error_message'] =
            $ok ? 'Level saved.' : 'Level could not be saved.';

        header('Location: /creator/rooms/' . $roomId . '/level');
        exit;
    }
}