<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IRoomService;

class CreatorController
{
    private IAuthService $authService;
    private IRoomService $roomService;

    public function __construct(
        IAuthService $authService,
        IRoomService $roomService
    ) {
        $this->authService = $authService;
        $this->roomService = $roomService;
    }

    /**
     * Show list of rooms created by the current user
     */
    public function listRooms(array $vars = []): void
    {
        $this->checkCreatorAccess();
        
        $userId = $_SESSION['user_id'];
        $rooms = $this->roomService->getRoomsByCreator($userId);
        
        require __DIR__ . '/../Views/creator/rooms.php';
    }

    /**
     * Show room creation form
     */
    public function createForm(array $vars = []): void
    {
        $this->checkCreatorAccess();
        require __DIR__ . '/../Views/rooms/index.php';
    }

    /**
     * Handle room creation form submission
     */
    public function createRoom(array $vars = []): void
    {
        $this->checkCreatorAccess();
        
        try {
            $roomData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'creator_id' => $_SESSION['user_id'],
                'difficulty' => $_POST['difficulty'] ?? 'medium',
                'estimated_time' => (int)($_POST['estimated_time'] ?? 30),
                'is_published' => isset($_POST['is_published']) && $_POST['is_published'] === '1'
            ];
            
            $roomId = $this->roomService->createRoom($roomData);
            
            $_SESSION['success_message'] = 'Room created successfully!';
            header('Location: /creator/rooms');
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: /creator/rooms/create');
            exit;
        }
    }

    /**
     * Show room edit form
     */
    public function editForm(array $vars = []): void
    {
        $this->checkCreatorAccess();
        
        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);
        
        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }
        
        // Check if user owns this room
        if ($room->creator_id !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }
        
        require __DIR__ . '/../Views/creator/edit.php';
    }

    /**
     * Handle room update form submission
     */
    public function updateRoom(array $vars = []): void
    {
        $this->checkCreatorAccess();
        
        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);
        
        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }
        
        // Check if user owns this room
        if ($room->creator_id !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'You can only edit your own rooms.';
            header('Location: /creator/rooms');
            exit;
        }
        
        try {
            // Update room properties
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

    /**
     * Handle room deletion
     */
    public function deleteRoom(array $vars = []): void
    {
        $this->checkCreatorAccess();
        
        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);
        
        if (!$room) {
            $_SESSION['error_message'] = 'Room not found.';
            header('Location: /creator/rooms');
            exit;
        }
        
        // Check if user owns this room
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

    /**
     * Check if user has creator/admin access
     */
    private function checkCreatorAccess(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $_SESSION['error_message'] = 'Please login to access creator features.';
            header('Location: /login');
            exit;
        }
        
        $user = $this->authService->getCurrentUser();
        if (!$user || ($user->role !== 'creator' && $user->role !== 'admin')) {
            $_SESSION['error_message'] = 'Access denied. Creator privileges required.';
            header('Location: /');
            exit;
        }
    }
}