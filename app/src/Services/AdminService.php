<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\RoomRepository;

class AdminService implements IAdminService
{
    private UserRepository $userRepository;
    private RoomRepository $roomRepository;

    public function __construct(
        UserRepository $userRepository,
        RoomRepository $roomRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roomRepository = $roomRepository;
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function updateUserRole(int $userId, string $role): bool
    {
        return $this->userRepository->updateRole($userId, $role);
    }

    public function getAllRooms(): array
    {
        return $this->roomRepository->getAll();
    }

    public function toggleRoomPublish(int $roomId, bool $publish): bool
    {
        return $this->roomRepository->togglePublish($roomId, $publish);
    }

    public function getDashboardStats(): array
    {
        $allUsers = $this->userRepository->getAll();
        $allRooms = $this->roomRepository->getAll();
        
        // Count published rooms
        $publishedRooms = 0;
        foreach ($allRooms as $room) {
            if ($room->is_published) {
                $publishedRooms++;
            }
        }
        
        // For now, active games can be 0 or you can implement tracking
        $activeGames = 0;
        
        return [
            'user_count' => count($allUsers),
            'room_count' => count($allRooms),
            'published_rooms' => $publishedRooms,
            'active_games' => $activeGames
        ];
    }
}