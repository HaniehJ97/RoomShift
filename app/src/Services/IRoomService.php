<?php

namespace App\Services;

use App\Models\RoomModel;

interface IRoomService
{
    public function getAllRooms(): array;
    public function getPublishedRooms(): array;
    public function getRoomsByCreator(int $creatorId): array;
    public function getRoomById(int $id): ?RoomModel;
    public function createRoom(array $roomData): int;
    public function updateRoom(RoomModel $room): void;
    public function deleteRoom(int $id): void;
    public function togglePublish(int $roomId, bool $publish): bool;
}