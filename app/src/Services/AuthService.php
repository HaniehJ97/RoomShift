<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\IUserRepository;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;
    private const SESSION_TIMEOUT = 24 * 60 * 60; // 24 hours

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        // REMOVED: $this->initSessionSecurity(); // This causes the error
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
        
        $userId = $this->userRepository->create($user);
        
        // Auto-login after registration
        $this->login($user->email, $data['password']);
        
        return $userId;
    }

    public function login(string $email, string $password): ?UserModel
    {
        $user = $this->userRepository->getByEmail($email);
        
        if (!$user) {
            // Prevent timing attacks by still hashing
            password_verify($password, '$2y$10$fakehashforsecurity');
            return null;
        }
        
        if (!$user->verifyPassword($password)) {
            return null;
        }
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Store user in session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['login_time'] = time();
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Set secure session cookie
        $this->setSecureSessionCookie();
        
        // Create remember me token if requested
        if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
            $this->createRememberMeToken($user->id);
        }
        
        return $user;
    }

    public function logout(): void
    {
        // Delete remember me token
        if (isset($_COOKIE['remember_token'])) {
            $this->deleteRememberMeToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }
        
        // Clear session data
        $_SESSION = [];
        
        // Destroy session
        if (session_id() !== '') {
            session_destroy();
        }
        
        // Delete session cookie
        $this->deleteSessionCookie();
    }

    public function isLoggedIn(): bool
    {
        // Check if session exists
        if (!isset($_SESSION['user_id'])) {
            // Check for remember me token
            if (isset($_COOKIE['remember_token'])) {
                return $this->validateRememberMeToken($_COOKIE['remember_token']);
            }
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time']) && 
            (time() - $_SESSION['login_time'] > self::SESSION_TIMEOUT)) {
            $this->logout();
            return false;
        }
        
        // Check for session hijacking
        if (!$this->validateSessionSecurity()) {
            $this->logout();
            return false;
        }
        
        // Update login time to extend session
        $_SESSION['login_time'] = time();
        
        return true;
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
        // First check if logged in
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Then check role
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        return ($_SESSION['user_role'] === 'admin');
    }

    // ========== PRIVATE METHODS ==========
    
    private function setSecureSessionCookie(): void
    {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            session_id(),
            [
                'expires' => time() + self::SESSION_TIMEOUT,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }
    
    private function deleteSessionCookie(): void
    {
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
    
    private function validateSessionSecurity(): bool
    {
        // Check user agent
        if (!isset($_SESSION['user_agent']) || 
            $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            return false;
        }
        
        return true;
    }
    
    private function createRememberMeToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Store token in database (you'll need to create a remember_tokens table)
        // $this->userRepository->saveRememberToken($userId, $hashedToken, $expires);
        
        // Set cookie
        setcookie('remember_token', $token, [
            'expires' => $expires,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    private function validateRememberMeToken(string $token): bool
    {
        // For now, return false - implement when you have database table
        return false;
    }
    
    private function deleteRememberMeToken(string $token): void
    {
        // Implement when you have database table
    }
}