<?php

namespace App\Repositories;

use App\Framework\Repository;
use PDO;

class RoomLevelRepository extends Repository implements IRoomLevelRepository
{
    public function findByRoomId(int $roomId): ?array
    {
        $sql = 'SELECT room_id, grid_width, grid_height, difficulty, config_json
                FROM room_levels
                WHERE room_id = :room_id
                LIMIT 1';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute([':room_id' => $roomId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function upsert(
        int $roomId,
        int $gridWidth,
        int $gridHeight,
        string $difficulty,
        string $configJson
    ): bool {
        $sql = 'INSERT INTO room_levels (room_id, grid_width, grid_height, difficulty, config_json)
                VALUES (:room_id, :grid_width, :grid_height, :difficulty, :config_json)
                ON DUPLICATE KEY UPDATE
                    grid_width = VALUES(grid_width),
                    grid_height = VALUES(grid_height),
                    difficulty = VALUES(difficulty),
                    config_json = VALUES(config_json)';

        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->execute([
            ':room_id' => $roomId,
            ':grid_width' => $gridWidth,
            ':grid_height' => $gridHeight,
            ':difficulty' => $difficulty,
            ':config_json' => $configJson
        ]);
    }
}