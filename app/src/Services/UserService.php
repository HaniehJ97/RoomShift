<?php

namespace App\Services;

use App\Repositories\IUserRepository;
use App\Models\UserModel;

class UserService implements IUserService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserName(int $userId): string
    {
        $user = $this->userRepository->findById($userId);
        if ($user) {
            return $user->name;
        }
        return "User " . $userId;
    }

    public function getUserById(int $id): ?UserModel
    {
        return $this->userRepository->findById($id);
    }

    public function canUserEditRoom(int $userId, int $roomCreatorId): bool
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            return false;
        }
        
        // Admin can edit any room, creators can edit their own rooms
        if ($user->role === UserModel::ROLE_ADMIN) {
            return true;
        }
        
        return $userId === $roomCreatorId;
    }

    public function isUserAdmin(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);
        return $user && $user->role === UserModel::ROLE_ADMIN;
    }
}