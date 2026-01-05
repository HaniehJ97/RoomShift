<?php

namespace App\Models;

class UserModel
{
    public ?int $id;
    public string $email;
    public string $password_hash;
    public string $name;
    public string $role;
    public string $created_at;
    public string $updated_at;

    public const ROLE_PLAYER = 'player';
    public const ROLE_CREATOR = 'creator';
    public const ROLE_ADMIN = 'admin';

    public function __construct(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        $this->email = $data['email'] ?? '';
        $this->password_hash = $data['password_hash'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->role = $data['role'] ?? self::ROLE_PLAYER;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
    }

    public function validate(): void
    {
        $email = trim($this->email);
        $name = trim($this->name);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('Name is required.');
        }

        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name must be at least 2 characters long.');
        }

        $validRoles = [self::ROLE_PLAYER, self::ROLE_CREATOR, self::ROLE_ADMIN];
        if (!in_array($this->role, $validRoles, true)) {
            $this->role = self::ROLE_PLAYER;
        }
    }

    public function setPassword(string $plainPassword): void
    {
        $plainPassword = trim($plainPassword);

        if ($plainPassword === '') {
            throw new \InvalidArgumentException('Password is required.');
        }

        if (strlen($plainPassword) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long.');
        }

        $this->password_hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password_hash);
    }
}