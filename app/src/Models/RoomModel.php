<?php

namespace App\Models;

class RoomModel
{
    public ?int $id;
    public string $title;
    public string $description;
    public string $created_at;

    public function __construct(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $this->id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function validate(): void
    {
        $title = trim($this->title);
        $description = trim($this->description);

        if (strlen($title) < 3) {
            throw new \InvalidArgumentException('Title must be at least 3 characters long.');
        }

        if (strlen($description) < 10) {
            throw new \InvalidArgumentException('Description must be at least 10 characters long.');
        }
    }
}