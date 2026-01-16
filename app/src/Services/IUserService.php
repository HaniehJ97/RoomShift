<?php

namespace App\Services;

use App\Models\UserModel;

interface IUserService
{
    public function getUserName(int $userId): string;
    public function getUserById(int $id): ?UserModel;
    public function canUserEditRoom(int $userId, int $roomCreatorId): bool;
    public function isUserAdmin(int $userId): bool;
}