<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\UserModel;
use PDO;

class UserRepository extends Repository implements IUserRepository
{
    // Fetch all users from the database
    public function getAll(): array
    {
        $sql = 'SELECT id, name, email, role, created_at FROM users ORDER BY id DESC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($rows as $row) {
            $users[] = new UserModel($row);
        }

        return $users;
    }

    // Find a user by their ID
    public function findById(int $id): ?UserModel
    {
        $sql = 'SELECT id, name, email, password_hash, role, created_at FROM users WHERE id = :id LIMIT 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new UserModel($row);
    }

    // Find a user by their email
    public function findByEmail(string $email): ?UserModel
    {
        $sql = 'SELECT id, name, email, password_hash, role, created_at FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':email' => $email]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new UserModel($row);
    }

    // Create a new user in the database
    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (name, email, password_hash, role, created_at)
                VALUES (:name, :email, :password_hash, :role, NOW())';

        $stmt = $this->getConnection()->prepare($sql);

        $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':email' => $data['email'] ?? '',
            ':password_hash' => $data['password_hash'] ?? '',
            ':role' => $data['role'] ?? UserModel::ROLE_PLAYER
        ]);

        return (int)$this->getConnection()->lastInsertId();
    }

    // Update a user's role
    public function updateRole(int $userId, string $role): bool
    {
        $allowedRoles = ['player', 'admin'];
        if (!in_array($role, $allowedRoles, true)) {
            return false;
        }

        $sql = 'UPDATE users SET role = :role WHERE id = :id';
        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->execute([
            ':role' => $role,
            ':id' => $userId
        ]);
    }

    // lightweight query for getting only users name by id
    public function getUserNameById(int $id): ?string
    {
        $sql = 'SELECT name FROM users WHERE id = :id LIMIT 1';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['name'] : null;
    }
}