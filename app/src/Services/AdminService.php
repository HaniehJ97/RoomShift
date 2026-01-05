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
        return [
            'users' => count($this->userRepository->getAll()),
            'rooms' => count($this->roomRepository->getAll())
        ];
    }
}