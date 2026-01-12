<?php

namespace App\Repositories;

interface IRoomLevelRepository
{
    public function findByRoomId(int $roomId): ?array;

    public function upsert(
        int $roomId,
        int $gridWidth,
        int $gridHeight,
        string $difficulty,
        string $configJson
    ): bool;
}