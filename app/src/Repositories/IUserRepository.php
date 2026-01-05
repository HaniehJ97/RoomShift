<?php

namespace App\Repositories;

use App\Models\UserModel;

interface IUserRepository
{
    public function getAll(): array;

    public function findById(int $id): ?UserModel;

    public function findByEmail(string $email): ?UserModel;

    public function create(array $data): int;

    public function updateRole(int $userId, string $role): bool;
}