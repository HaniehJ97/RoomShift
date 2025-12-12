<?php

namespace App\Repositories;

use App\Models\UserModel;

interface IUserRepository
{
    public function getAll(): array;
    public function getById(int $id): ?UserModel;
    public function getByEmail(string $email): ?UserModel;
    public function create(UserModel $user): int;
    public function update(UserModel $user): void;
    public function delete(int $id): void;
}