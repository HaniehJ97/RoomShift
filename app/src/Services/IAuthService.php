<?php

namespace App\Services;

use App\Models\UserModel;

interface IAuthService
{
    public function register(array $data): int;
    public function login(string $email, string $password): ?UserModel;
    public function logout(): void;
    public function isLoggedIn(): bool;
    public function getCurrentUser(): ?UserModel;
    public function isAdmin(): bool;
}