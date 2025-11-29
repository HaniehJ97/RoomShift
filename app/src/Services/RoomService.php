<?php

namespace App\Services;

use App\Models\RoomModel;
use App\Repositories\RoomRepository;

class RoomService implements IRoomService
{
    private \App\Repositories\IRoomRepository $roomRepository;

    public function __construct()
    {
        $this->roomRepository = new RoomRepository();
    }

    public function getAllRooms(): array
    {
        return $this->roomRepository->getAll();
    }

    public function getRoomById(int $id): ?RoomModel
    {
        return $this->roomRepository->getById($id);
    }

    public function createRoom(RoomModel $room): int
    {
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
}