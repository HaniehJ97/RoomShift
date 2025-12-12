<?php

namespace App\Models;

class UserModel
{
    public ?int $id;
    public string $email;
    public string $password;
    public string $name;
    public string $role;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->role = $data['role'] ?? 'user';
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
    }

    public function validate(): void
    {
        $email = trim($this->email);
        $password = trim($this->password);
        $name = trim($this->name);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address.');
        }

        if (empty($password)) {
            throw new \InvalidArgumentException('Password is required.');
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long.');
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('Name is required.');
        }

        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name must be at least 2 characters long.');
        }
    }

    public function hashPassword(): void
    {
        if (!empty($this->password)) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
}