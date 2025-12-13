<?php

namespace App\Models;

class RoomModel
{
    public ?int $id;
    public string $title;
    public string $description;
    public int $creator_id;
    public bool $is_published;
    public string $difficulty;
    public int $estimated_time;
    public ?int $starting_state_id;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->creator_id = (int)($data['creator_id'] ?? 0);
        $this->is_published = (bool)($data['is_published'] ?? false);
        $this->difficulty = $data['difficulty'] ?? 'medium';
        $this->estimated_time = (int)($data['estimated_time'] ?? 30);
        $this->starting_state_id = isset($data['starting_state_id']) ? (int)$data['starting_state_id'] : null;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
    }

    public function validate(): void
    {
        if (empty(trim($this->title))) {
            throw new \InvalidArgumentException('Room title is required.');
        }

        if (strlen($this->title) < 3) {
            throw new \InvalidArgumentException('Room title must be at least 3 characters.');
        }

        if (empty(trim($this->description))) {
            throw new \InvalidArgumentException('Room description is required.');
        }

        if ($this->creator_id <= 0) {
            throw new \InvalidArgumentException('Creator ID is required.');
        }

        // Validate difficulty
        $validDifficulties = ['easy', 'medium', 'hard'];
        if (!in_array($this->difficulty, $validDifficulties)) {
            $this->difficulty = 'medium';
        }

        // Validate estimated time
        if ($this->estimated_time < 1) {
            $this->estimated_time = 30;
        }
    }
}