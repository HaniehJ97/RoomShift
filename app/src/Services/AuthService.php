<?php

namespace App\Services;

use App\Repositories\IUserRepository;
use App\Models\UserModel;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $email, string $password): ?UserModel
    {
        $email = trim($email);

        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            return null;
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        // Important: regenerate session AFTER successful login
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;

        return $user;
    }

    public function register(array $data): int
    {
        $user = new UserModel([
            'email' => $data['email'] ?? '',
            'name'  => $data['name'] ?? '',
            'role'  => UserModel::ROLE_PLAYER
        ]);

        $user->validate();
        $user->setPassword($data['password'] ?? '');

        return $this->userRepository->create([
            'email'         => $user->email,
            'name'          => $user->name,
            'password_hash' => $user->password_hash,
            'role'          => $user->role
        ]);
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === UserModel::ROLE_ADMIN;
    }

    public function getCurrentUser(): ?UserModel
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->userRepository->findById((int)$_SESSION['user_id']);
    }
}