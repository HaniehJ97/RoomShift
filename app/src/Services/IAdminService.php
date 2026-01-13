<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\RoomModel;

interface IAdminService
{
    public function getAllUsers(): array;
    public function updateUserRole(int $userId, string $role): bool;
    public function getAllRooms(): array;
    public function toggleRoomPublish(int $roomId, bool $publish): bool;
    public function getDashboardStats(): array;
}