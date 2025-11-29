<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\RoomModel;
use PDO;

class RoomRepository extends Repository implements IRoomRepository
{
    public function getAll(): array
    {
        $query = 'SELECT id, title, description, created_at 
                  FROM rooms 
                  ORDER BY created_at DESC';
        
        $statement = $this->getConnection()->query($query);
        return $statement->fetchAll(PDO::FETCH_CLASS, RoomModel::class);
    }

    public function getById(int $id): ?RoomModel
    {
        $query = 'SELECT id, title, description, created_at 
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
        $query = 'INSERT INTO rooms (title, description, created_at) 
                  VALUES (:title, :description, :created_at)';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':title', $room->title, PDO::PARAM_STR);
        $statement->bindValue(':description', $room->description, PDO::PARAM_STR);
        $statement->bindValue(':created_at', $room->created_at, PDO::PARAM_STR);
        $statement->execute();
        
        return $this->getConnection()->lastInsertId();
    }

    public function update(RoomModel $room): void
    {
        $query = 'UPDATE rooms 
                  SET title = :title, description = :description 
                  WHERE id = :id';
        
        $statement = $this->getConnection()->prepare($query);
        $statement->bindValue(':title', $room->title, PDO::PARAM_STR);
        $statement->bindValue(':description', $room->description, PDO::PARAM_STR);
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
}