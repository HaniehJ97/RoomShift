<?php

namespace App\Services;

interface IAdminService
{
    public function getDashboardStats(): array;
    public function getAllUsers(): array;
    public function getAllRooms(): array;
    public function updateUserRole(int $userId, string $role): bool;
    public function deleteUser(int $userId): bool;
    public function toggleRoomPublish(int $roomId, bool $publish): bool;
    public function deleteRoom(int $roomId): bool;
}