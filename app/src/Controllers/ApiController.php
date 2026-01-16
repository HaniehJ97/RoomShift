<?php

namespace App\Controllers;

use App\Services\IAuthService;
use App\Services\IRoomService;
use App\Services\IUserService;

class ApiController
{
    private IAuthService $authService;
    private IRoomService $roomService;
    private IUserService $userService;

    public function __construct(
        IAuthService $authService,
        IRoomService $roomService,
        IUserService $userService
    ) {
        $this->authService = $authService;
        $this->roomService = $roomService;
        $this->userService = $userService;
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function requireAjax(): void
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Invalid request type'
            ], 400);
        }
    }

    private function requireLogin(): void
    {
        if (!$this->authService->isLoggedIn()) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }
    }

    private function requireAdmin(): void
    {
        if (!$this->authService->isAdmin()) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Admin privileges required'
            ], 403);
        }
    }

    private function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (!hash_equals($sessionToken, $token)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'CSRF token mismatch'
            ], 403);
        }
    }

    // GET /api/rooms/{id}
    public function getRoom(array $vars = []): void
    {
        $this->requireAjax();
        
        $roomId = (int)($vars['id'] ?? 0);
        $room = $this->roomService->getRoomById($roomId);

        if (!$room) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // Check if user can access this room
        $userId = $this->authService->getCurrentUser()?->id ?? 0;
        $isAdmin = $this->authService->isAdmin();
        
        if (!$this->roomService->canUserAccessRoom($userId, $room, $isAdmin)) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $this->jsonResponse([
            'success' => true,
            'room' => [
                'id' => $room->id,
                'title' => $room->title,
                'description' => $room->description,
                'difficulty' => $room->difficulty,
                'estimated_time' => $room->estimated_time,
                'creator_id' => $room->creator_id,
                'is_published' => $room->is_published,
                'created_at' => $room->created_at,
                'level_config' => $room->level_config
            ]
        ]);
    }

    // POST /api/rooms
    public function createRoom(array $vars = []): void
    {
        $this->requireAjax();
        $this->requireLogin();
        $this->requireAdmin();
        $this->validateCsrf();

        try {
            $userId = $this->authService->getCurrentUser()->id;
            $roomId = $this->roomService->createRoomFromPostData($_POST, $userId);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'Room created successfully',
                'room_id' => $roomId,
                'redirect' => "/rooms/{$roomId}/play"
            ]);
            
        } catch (\InvalidArgumentException $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            error_log('API room creation error: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}