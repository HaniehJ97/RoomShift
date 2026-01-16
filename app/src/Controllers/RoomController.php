<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IRoomService;

class RoomController
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

    private function requireLogin(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $_SESSION['error_message'] = 'Please login first.';
            header('Location: /login');
            exit;
        }
    }

    // PLAYER ACTIONS ONLY
    public function index(array $vars = []): void
    {
        $isLoggedIn = $this->authService->isLoggedIn();
        $rooms = [];

        if ($isLoggedIn) {
            $user = $this->authService->getCurrentUser();
            // Admin can see all rooms (including drafts)
            if ($user && $user->role === 'admin') {
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

        // Only admins can play unpublished rooms
        if (!$room->is_published && !$this->authService->isAdmin()) {
            $_SESSION['error_message'] = 'This room is not published yet.';
            header('Location: /rooms');
            exit;
        }

        // Get creator name 
        $creatorName = $this->getCreatorName($room->creator_id);
        
        // Pass data to view
        require __DIR__ . '/../Views/rooms/play.php';
    }
    private function getCreatorName(int $creatorId): string
    {
        return "User " . $creatorId;
    }
}