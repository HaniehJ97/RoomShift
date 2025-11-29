<?php

namespace App\Services;

use App\Models\RoomModel;

interface IRoomService
{
    public function getAllRooms(): array;
    public function getRoomById(int $id): ?RoomModel;
    public function createRoom(RoomModel $room): int;
    public function updateRoom(RoomModel $room): void;
    public function deleteRoom(int $id): void;
}