<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\RoomModel;
use PDO;

class RoomRepository extends Repository implements IRoomRepository
{
    public function getAll(): array
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, starting_state_id,
                         created_at, updated_at 
                  FROM rooms 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->query($query);
        return $statement->fetchAll(PDO::FETCH_CLASS, RoomModel::class);
    }

    public function getById(int $id): ?RoomModel
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, starting_state_id,
                         created_at, updated_at 
                  FROM rooms 
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        $statement->setFetchMode(PDO::FETCH_CLASS, RoomModel::class);
        return $statement->fetch() ?: null;
    }

    public function create(RoomModel $room): int
    {
        $query = 'INSERT INTO rooms 
                  (title, description, creator_id, is_published, 
                   difficulty, estimated_time, created_at, updated_at) 
                  VALUES (:title, :description, :creator_id, :is_published,
                          :difficulty, :estimated_time, :created_at, :updated_at)';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':title', $room->title, PDO::PARAM_STR);
        $statement->bindValue(':description', $room->description, PDO::PARAM_STR);
        $statement->bindValue(':creator_id', $room->creator_id, PDO::PARAM_INT);
        $statement->bindValue(':is_published', $room->is_published ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue(':difficulty', $room->difficulty, PDO::PARAM_STR);
        $statement->bindValue(':estimated_time', $room->estimated_time, PDO::PARAM_INT);
        $statement->bindValue(':created_at', $room->created_at, PDO::PARAM_STR);
        $statement->bindValue(':updated_at', $room->updated_at, PDO::PARAM_STR);
        $statement->execute();
        
        return $this->getConnection()->lastInsertId();
    }

    public function update(RoomModel $room): void
    {
        $query = 'UPDATE rooms 
                  SET title = :title, 
                      description = :description,
                      is_published = :is_published,
                      difficulty = :difficulty,
                      estimated_time = :estimated_time,
                      updated_at = :updated_at
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':title', $room->title, PDO::PARAM_STR);
        $statement->bindValue(':description', $room->description, PDO::PARAM_STR);
        $statement->bindValue(':is_published', $room->is_published ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue(':difficulty', $room->difficulty, PDO::PARAM_STR);
        $statement->bindValue(':estimated_time', $room->estimated_time, PDO::PARAM_INT);
        $statement->bindValue(':updated_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':id', $room->id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $query = 'DELETE FROM rooms WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    // ====== NEW METHODS ======
    
    public function getPublishedRooms(): array
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, starting_state_id,
                         created_at, updated_at 
                  FROM rooms 
                  WHERE is_published = 1 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->query($query);
        return $statement->fetchAll(PDO::FETCH_CLASS, RoomModel::class);
    }
    
    public function getRoomsByCreator(int $creatorId): array
    {
        $query = 'SELECT id, title, description, creator_id, is_published, 
                         difficulty, estimated_time, starting_state_id,
                         created_at, updated_at 
                  FROM rooms 
                  WHERE creator_id = :creator_id 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':creator_id', $creatorId, PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_CLASS, RoomModel::class);
    }
    
    public function togglePublish(int $roomId, bool $publish): bool
    {
        $query = 'UPDATE rooms 
                  SET is_published = :is_published,
                      updated_at = :updated_at
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':is_published', $publish ? 1 : 0, PDO::PARAM_INT);
        $statement->bindValue(':updated_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue(':id', $roomId, PDO::PARAM_INT);
        
        return $statement->execute();
    }
}