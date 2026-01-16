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
        $this->validateRoomData($roomData);
        
        $room = new RoomModel($roomData);

        return $this->roomRepository->create($room);
    }
    
    public function createRoomReturnId(array $roomData): int
    {
        return $this->createRoom($roomData);
    }

    public function updateRoom(RoomModel $room): bool
    {
        $this->validateRoomData([
            'title' => $room->title,
            'description' => $room->description,
            'creator_id' => $room->creator_id,
            'difficulty' => $room->difficulty,
            'estimated_time' => $room->estimated_time,
            'level_config' => $room->level_config
        ]);

        return $this->roomRepository->update($room);
    }

    private function validateRoomData(array $data): void
    {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $creatorId = (int)($data['creator_id'] ?? 0);
        $difficulty = $data['difficulty'] ?? 'easy';
        $estimatedTime = (int)($data['estimated_time'] ?? 30);
        $levelConfig = $data['level_config'] ?? [];

        if (empty($title)) {
            throw new \InvalidArgumentException('Room title is required.');
        }

        if (strlen($title) < 3) {
            throw new \InvalidArgumentException('Room title must be at least 3 characters.');
        }

        if (empty($description)) {
            throw new \InvalidArgumentException('Room description is required.');
        }

        if ($creatorId <= 0) {
            throw new \InvalidArgumentException('Creator ID is required.');
        }

        // Validate difficulty
        $validDifficulties = ['easy', 'medium', 'hard'];
        if (!in_array($difficulty, $validDifficulties)) {
            throw new \InvalidArgumentException('Invalid difficulty level.');
        }

        // Validate estimated time
        if ($estimatedTime < 1) {
            throw new \InvalidArgumentException('Estimated time must be at least 1 minute.');
        }
        
        // Validate level config
        $this->validateLevelConfig($levelConfig);
    }
    
    private function validateLevelConfig(array $config): void
    {
        // Ensure grid dimensions are reasonable
        $gridWidth = max(8, min(30, (int)($config['grid_width'] ?? 12)));
        $gridHeight = max(8, min(30, (int)($config['grid_height'] ?? 12)));
        
        // Ensure arrays exist
        $walls = $config['walls'] ?? [];
        $bombs = $config['bombs'] ?? [];
        
        // Ensure key and door exist
        $key = $config['key'] ?? ['x' => 0, 'y' => 0];
        $door = $config['door'] ?? ['x' => 11, 'y' => 11];
        
        // Validate coordinates are integers
        if (!isset($key['x']) || !isset($key['y'])) {
            throw new \InvalidArgumentException('Key position must have x and y coordinates.');
        }
        
        if (!isset($door['x']) || !isset($door['y'])) {
            throw new \InvalidArgumentException('Door position must have x and y coordinates.');
        }
        
        $keyX = (int)$key['x'];
        $keyY = (int)$key['y'];
        $doorX = (int)$door['x'];
        $doorY = (int)$door['y'];
        
        // Ensure key and door are within grid
        $maxX = $gridWidth - 1;
        $maxY = $gridHeight - 1;
        
        if ($keyX < 0 || $keyX > $maxX || $keyY < 0 || $keyY > $maxY) {
            throw new \InvalidArgumentException('Key position is outside the grid.');
        }
        
        if ($doorX < 0 || $doorX > $maxX || $doorY < 0 || $doorY > $maxY) {
            throw new \InvalidArgumentException('Door position is outside the grid.');
        }
        
        // Prevent key and door from being in same spot
        if ($keyX === $doorX && $keyY === $doorY) {
            throw new \InvalidArgumentException('Key and door cannot be in the same position.');
        }
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