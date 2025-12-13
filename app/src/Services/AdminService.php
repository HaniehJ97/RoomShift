<?php

namespace App\Services;

use App\Repositories\IUserRepository;
use App\Repositories\IRoomRepository;

class AdminService implements IAdminService
{
    private IUserRepository $userRepository;
    private IRoomRepository $roomRepository;

    public function __construct(
        IUserRepository $userRepository,
        IRoomRepository $roomRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roomRepository = $roomRepository;
    }

    public function getDashboardStats(): array
    {
        return [
            'user_count' => count($this->userRepository->getAll()),
            'room_count' => count($this->roomRepository->getAll()),
            'published_rooms' => count($this->roomRepository->getPublishedRooms()),
            'active_games' => 0 // You'll implement this later
        ];
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->getAll();
    }

    public function getAllRooms(): array
    {
        return $this->roomRepository->getAll();
    }

    public function updateUserRole(int $userId, string $role): bool
    {
        $user = $this->userRepository->getById($userId);
        if (!$user) {
            return false;
        }
        
        $user->role = $role;
        $this->userRepository->update($user);
        return true;
    }

    public function deleteUser(int $userId): bool
    {
        try {
            $this->userRepository->delete($userId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function toggleRoomPublish(int $roomId, bool $publish): bool
    {
        return $this->roomRepository->togglePublish($roomId, $publish);
    }

    public function deleteRoom(int $roomId): bool
    {
        try {
            $this->roomRepository->delete($roomId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}