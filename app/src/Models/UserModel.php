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
    public const ROLE_ADMIN = 'admin';

    //constructor to initialize properties from an array
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

    
    public function setPassword(string $plainPassword): void
    {
        // Just hash the password and store it
        $this->password_hash = password_hash(trim($plainPassword), PASSWORD_DEFAULT);
    }

    //verifies a plain password against the stored hash
    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password_hash);
    }
}