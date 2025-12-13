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

    public function index(array $vars = []): void
    {
        // Check if user is logged in
        $isLoggedIn = $this->authService->isLoggedIn();
        $isCreator = false;
        $isAdmin = false;
        
        if ($isLoggedIn) {
            $user = $this->authService->getCurrentUser();
            $isCreator = $user && ($user->role === 'creator' || $user->role === 'admin');
            $isAdmin = $user && $user->role === 'admin';
        }
        
        // Get rooms based on user role
        if ($isCreator || $isAdmin) {
            // Show ALL rooms to creators/admins
            $rooms = $this->roomService->getAllRooms();
        } else {
            // Show only PUBLISHED rooms to public/players
            $rooms = $this->roomService->getPublishedRooms();
        }
        
        require __DIR__ . '/../Views/rooms/index.php';
    }

    public function store(array $vars = []): void
    {
        // For now, keep your existing store method
        // But update it to use RoomService
        try {
            $roomData = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'creator_id' => $_SESSION['user_id'] ?? 1, // Default to admin if not logged in
                'difficulty' => $_POST['difficulty'] ?? 'medium',
                'estimated_time' => (int)($_POST['estimated_time'] ?? 30),
                'is_published' => isset($_POST['is_published']) && $_POST['is_published'] === '1'
            ];
            
            $roomId = $this->roomService->createRoom($roomData);
            
            $_SESSION['success_message'] = 'Room created successfully!';
            header('Location: /rooms');
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $error = $e->getMessage();
            // Pass error to view
            require __DIR__ . '/../Views/rooms/index.php';
        }
    }
}