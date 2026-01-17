<?php

namespace App\Services;

use App\Repositories\IUserRepository;
use App\Models\UserModel;

class UserService implements IUserService
{
    private IUserRepository $userRepository;

    //stores the user repository dependency
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //returns all users name by id
    public function getUserName(int $userId): string
    {
        $user = $this->userRepository->findById($userId);
        if ($user) {
            return $user->name;
        }
        return "User " . $userId;
    }

    //returns a user by their ID or null if not found
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

    //checks if the user role is admin returns true
    public function isUserAdmin(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);
        return $user && $user->role === UserModel::ROLE_ADMIN;
    }
}