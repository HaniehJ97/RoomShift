<?php

namespace App\Repositories;

use App\Models\RoomModel;

interface IRoomRepository
{
    public function getAll(): array;
    public function getById(int $id): ?RoomModel;
    public function create(RoomModel $room): int;
    public function update(RoomModel $room): void;
    public function delete(int $id): void;
}