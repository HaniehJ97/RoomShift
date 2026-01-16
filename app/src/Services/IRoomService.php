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
    public function createRoomFromPostData(array $postData, int $creatorId): int; 
    public function updateRoom(RoomModel $room): bool;
    public function updateRoomFromPostData(int $roomId, array $postData): bool; 
    public function deleteRoom(int $id): void;
    public function createRoomReturnId(array $roomData): int;
    public function togglePublish(int $roomId, bool $publish): bool;
    public function canUserAccessRoom(int $userId, RoomModel $room, bool $isAdmin): bool; 
}