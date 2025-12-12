<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\IUserRepository;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): int
    {
        $user = new UserModel($data);
        $user->validate();
        $user->hashPassword();
        
        // Check if email already exists
        $existingUser = $this->userRepository->getByEmail($user->email);
        if ($existingUser) {
            throw new \InvalidArgumentException('Email already registered.');
        }
        
        return $this->userRepository->create($user);
    }

    public function login(string $email, string $password): ?UserModel
    {
        $user = $this->userRepository->getByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        if (!$user->verifyPassword($password)) {
            return null;
        }
        
        // Store user in session (without password)
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        
        return $user;
    }

    public function logout(): void
    {
        // Clear session data
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        
        // Destroy session
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser(): ?UserModel
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userRepository->getById($_SESSION['user_id']);
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}