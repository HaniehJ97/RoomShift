<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\UserModel;
use PDO;

class UserRepository extends Repository implements IUserRepository
{
    public function getAll(): array
    {
        $query = 'SELECT id, email, name, role, created_at, updated_at 
                  FROM users 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->query($query);
        return $statement->fetchAll(PDO::FETCH_CLASS, UserModel::class);
    }

    public function getById(int $id): ?UserModel
    {
        $query = 'SELECT id, email, password, name, role, created_at, updated_at 
                  FROM users 
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        $statement->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        return $statement->fetch() ?: null;
    }

    public function getByEmail(string $email): ?UserModel
    {
        $query = 'SELECT id, email, password, name, role, created_at, updated_at 
                  FROM users 
                  WHERE email = :email';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        
        $statement->setFetchMode(PDO::FETCH_CLASS, UserModel::class);
        return $statement->fetch() ?: null;
    }

    public function create(UserModel $user): int
    {
        $query = 'INSERT INTO users (email, password, name, role, created_at, updated_at) 
                  VALUES (:email, :password, :name, :role, :created_at, :updated_at)';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':email', $user->email, PDO::PARAM_STR);
        $statement->bindValue(':password', $user->password, PDO::PARAM_STR);
        $statement->bindValue(':name', $user->name, PDO::PARAM_STR);
        $statement->bindValue(':role', $user->role, PDO::PARAM_STR);
        $statement->bindValue(':created_at', $user->created_at, PDO::PARAM_STR);
        $statement->bindValue(':updated_at', $user->updated_at, PDO::PARAM_STR);
        $statement->execute();
        
        return $this->getConnection()->lastInsertId();
    }

    public function update(UserModel $user): void
    {
        $query = 'UPDATE users 
                  SET email = :email, name = :name, role = :role, updated_at = :updated_at';
        
        if (!empty($user->password)) {
            $query .= ', password = :password';
        }
        
        $query .= ' WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':email', $user->email, PDO::PARAM_STR);
        $statement->bindValue(':name', $user->name, PDO::PARAM_STR);
        $statement->bindValue(':role', $user->role, PDO::PARAM_STR);
        $statement->bindValue(':updated_at', $user->updated_at, PDO::PARAM_STR);
        
        if (!empty($user->password)) {
            $statement->bindValue(':password', $user->password, PDO::PARAM_STR);
        }
        
        $statement->bindValue(':id', $user->id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = 'DELETE FROM users WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
}