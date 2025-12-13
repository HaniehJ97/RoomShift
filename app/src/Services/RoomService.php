<?php

namespace App\Services;

use App\Models\RoomModel;
use App\Repositories\IRoomRepository;

class RoomService implements IRoomService
{
    private IRoomRepository $roomRepository;

    public function __construct(IRoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function getAllRooms(): array
    {
        return $this->roomRepository->getAll();
    }

    public function getPublishedRooms(): array
    {
        return $this->roomRepository->getPublishedRooms();
    }

    public function getRoomsByCreator(int $creatorId): array
    {
        return $this->roomRepository->getRoomsByCreator($creatorId);
    }

    public function getRoomById(int $id): ?RoomModel
    {
        return $this->roomRepository->getById($id);
    }

    public function createRoom(array $roomData): int
    {
        $room = new RoomModel($roomData);
        $room->validate();
        return $this->roomRepository->create($room);
    }

    public function updateRoom(RoomModel $room): void
    {
        $room->validate();
        $this->roomRepository->update($room);
    }

    public function deleteRoom(int $id): void
    {
        $this->roomRepository->delete($id);
    }

    public function togglePublish(int $roomId, bool $publish): bool
    {
        return $this->roomRepository->togglePublish($roomId, $publish);
    }
}