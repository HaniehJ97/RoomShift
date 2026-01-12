<?php

namespace App\Services;

interface IRoomLevelService
{
    public function getByRoomId(int $roomId): ?array;

    public function saveLevel(
        int $roomId,
        int $gridWidth,
        int $gridHeight,
        string $difficulty,
        string $configJson
    ): bool;
}