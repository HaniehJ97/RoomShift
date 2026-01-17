<?php

namespace App\Services;

use App\Repositories\IUserRepository;
use App\Models\UserModel;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;

    //stores the user repository dependency
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //checks email+password, regenerates session id, and stores user data in session.
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

        //regenerate session after successful login
        session_regenerate_id(true);

        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;

        return $user;
    }

    //validates input, hashes password, builds a UserModel, then saves it.
    public function register(array $data): int
    {
        $this->validateRegistrationData($data);

        $email = $data['email'] ?? '';

        // Check if email already exists BEFORE inserting
        if ($this->userRepository->findByEmail($email)) {
            throw new \InvalidArgumentException('This email address is already registered.');
        }

        $user = new UserModel([
            'email' => $email,
            'name'  => $data['name'] ?? '',
            'role'  => UserModel::ROLE_PLAYER
        ]);

        $user->setPassword($data['password'] ?? '');

        return $this->userRepository->create([
            'email'         => $user->email,
            'name'          => $user->name,
            'password_hash' => $user->password_hash,
            'role'          => $user->role
        ]);
    }

    //validates registration input data
    private function validateRegistrationData(array $data): void
    {
        $email = trim($data['email'] ?? '');
        $name = trim($data['name'] ?? '');
        $password = trim($data['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('Name is required.');
        }

        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name must be at least 2 characters long.');
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required.');
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long.');
        }
    }

   //clears session data, deletes cookie, and destroys the session.
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

    //returns true if user_id exists in session.
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    //returns true if session role is admin
    public function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === UserModel::ROLE_ADMIN;
    }

    //loads the logged-in user from the database using session user_id
    public function getCurrentUser(): ?UserModel
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->userRepository->findById((int)$_SESSION['user_id']);
    }
}