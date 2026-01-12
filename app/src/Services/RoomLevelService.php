<?php

namespace App\Services;

use App\Repositories\IRoomLevelRepository;

class RoomLevelService implements IRoomLevelService
{
    private IRoomLevelRepository $roomLevelRepository;

    public function __construct(IRoomLevelRepository $roomLevelRepository)
    {
        $this->roomLevelRepository = $roomLevelRepository;
    }

    public function getByRoomId(int $roomId): ?array
    {
        return $this->roomLevelRepository->findByRoomId($roomId);
    }

    public function saveLevel(
        int $roomId,
        int $gridWidth,
        int $gridHeight,
        string $difficulty,
        string $configJson
    ): bool {
        if ($roomId <= 0) {
            return false;
        }

        if ($gridWidth < 8) $gridWidth = 8;
        if ($gridHeight < 8) $gridHeight = 8;
        if ($gridWidth > 30) $gridWidth = 30;
        if ($gridHeight > 30) $gridHeight = 30;

        $allowed = ['easy', 'medium', 'hard'];
        if (!in_array($difficulty, $allowed, true)) {
            $difficulty = 'easy';
        }

        $decoded = json_decode($configJson, true);
        if (!is_array($decoded)) {
            $decoded = ['walls' => [], 'bombs' => [], 'foods' => []];
        }

        if (!isset($decoded['walls']) || !is_array($decoded['walls'])) $decoded['walls'] = [];
        if (!isset($decoded['bombs']) || !is_array($decoded['bombs'])) $decoded['bombs'] = [];
        if (!isset($decoded['foods']) || !is_array($decoded['foods'])) $decoded['foods'] = [];

        $configJson = json_encode($decoded);

        return $this->roomLevelRepository->upsert($roomId, $gridWidth, $gridHeight, $difficulty, $configJson);
    }
}