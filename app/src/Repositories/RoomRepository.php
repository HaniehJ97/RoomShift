<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\RoomModel;
use PDO;

class RoomRepository extends Repository implements IRoomRepository
{
    public function getAll(): array
    {
        $sql = 'SELECT id, title, description, creator_id, difficulty, estimated_time, 
        is_published, created_at, level_config FROM rooms ORDER BY created_at DESC';
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rooms = [];

        foreach ($rows as $row) {
            // Decode level_config if it's stored as JSON
            if (isset($row['level_config']) && is_string($row['level_config'])) {
                $row['level_config'] = json_decode($row['level_config'], true);
            }
            $rooms[] = new RoomModel($row);
        }

        return $rooms;
    }

    public function getById(int $id): ?RoomModel
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, level_config,
                         created_at, updated_at 
                  FROM rooms 
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        
        return new RoomModel($row);
    }
    
    public function getPublishedRooms(): array
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, level_config,
                         created_at, updated_at 
                  FROM rooms 
                  WHERE is_published = 1 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->query($query);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $rooms = [];
        foreach ($rows as $row) {
            $rooms[] = new RoomModel($row);
        }
        
        return $rooms;
    }
    
    public function getRoomsByCreator(int $creatorId): array
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, level_config,
                         created_at, updated_at 
                  FROM rooms 
                  WHERE creator_id = :creator_id 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':creator_id', $creatorId, PDO::PARAM_INT);
        $statement->execute();
        
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $rooms = [];
        foreach ($rows as $row) {
            $rooms[] = new RoomModel($row);
        }
        
        return $rooms;
    }

    public function create(RoomModel $room): int
    {
        $query = 'INSERT INTO rooms 
                  (title, description, creator_id, is_published, 
                   difficulty, estimated_time, level_config, created_at, updated_at) 
                  VALUES (:title, :description, :creator_id, :is_published,
                          :difficulty, :estimated_time, :level_config, :created_at, :updated_at)';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':title', $room->title, PDO::PARAM_STR);
        $statement->bindValue(':description', $room->description, PDO::PARAM_STR);
        $statement->bindValue(':creator_id', $room->creator_id, PDO::PARAM_INT);
        $statement->bindValue(':is_published', $room->is_published ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue(':difficulty', $room->difficulty, PDO::PARAM_STR);
        $statement->bindValue(':estimated_time', $room->estimated_time, PDO::PARAM_INT);
        $statement->bindValue(':level_config', $room->getLevelConfigJson(), PDO::PARAM_STR);
        $statement->bindValue(':created_at', $room->created_at, PDO::PARAM_STR);
        $statement->bindValue(':updated_at', $room->updated_at, PDO::PARAM_STR);
        $statement->execute();
        
        return (int)$this->getConnection()->lastInsertId();
    }

    public function update(RoomModel $room): bool
    {
        $sql = 'UPDATE rooms
                SET title = :title,
                    description = :description,
                    difficulty = :difficulty,
                    estimated_time = :estimated_time,
                    is_published = :is_published,
                    level_config = :level_config,
                    updated_at = NOW()
                WHERE id = :id';

        $stmt = $this->getConnection()->prepare($sql);

        return $stmt->execute([
            ':title' => $room->title,
            ':description' => $room->description,
            ':difficulty' => $room->difficulty,
            ':estimated_time' => $room->estimated_time,
            ':is_published' => $room->is_published ? 1 : 0,
            ':level_config' => $room->getLevelConfigJson(),
            ':id' => $room->id
        ]);
    }

    public function delete(int $id): void
    {
        $query = 'DELETE FROM rooms WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }
    
    public function togglePublish(int $roomId, bool $publish): bool
    {
        $query = 'UPDATE rooms 
                  SET is_published = :is_published,
                      updated_at = NOW()
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':is_published', $publish ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue(':id', $roomId, PDO::PARAM_INT);
        
        return $statement->execute();
    }
    
    public function createRoomReturnId(RoomModel $room): int
    {
        return $this->create($room);
    }
}