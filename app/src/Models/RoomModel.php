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
    public array $level_config;  // NEW: stores grid, walls, bombs, key, door
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data = [])
    {
        // Set default level config
        $defaultConfig = [
            'grid_width' => 12,
            'grid_height' => 12,
            'walls' => [],
            'bombs' => [],
            'key' => ['x' => 0, 'y' => 0],
            'door' => ['x' => 11, 'y' => 11]
        ];

        // Basic properties
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->creator_id = (int)($data['creator_id'] ?? 0);
        $this->is_published = (bool)($data['is_published'] ?? false);
        $this->difficulty = $data['difficulty'] ?? 'easy';
        $this->estimated_time = (int)($data['estimated_time'] ?? 30);
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
        
        // Handle level_config
        $levelConfig = $data['level_config'] ?? $defaultConfig;
        if (is_string($levelConfig)) {
            $this->level_config = json_decode($levelConfig, true) ?? $defaultConfig;
        } else {
            $this->level_config = $levelConfig;
        }
        
        // Ensure all required fields exist
        $this->level_config = array_merge($defaultConfig, $this->level_config);
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
            $this->difficulty = 'easy';
        }

        // Validate estimated time
        if ($this->estimated_time < 1) {
            $this->estimated_time = 30;
        }
        
        // Validate level config
        $this->validateLevelConfig();
    }
    
    private function validateLevelConfig(): void
    {
        // Ensure grid dimensions are reasonable
        $this->level_config['grid_width'] = max(8, min(30, (int)($this->level_config['grid_width'] ?? 12)));
        $this->level_config['grid_height'] = max(8, min(30, (int)($this->level_config['grid_height'] ?? 12)));
        
        // Ensure arrays exist
        $this->level_config['walls'] = $this->level_config['walls'] ?? [];
        $this->level_config['bombs'] = $this->level_config['bombs'] ?? [];
        
        // Ensure key and door exist
        $this->level_config['key'] = $this->level_config['key'] ?? ['x' => 0, 'y' => 0];
        $this->level_config['door'] = $this->level_config['door'] ?? ['x' => 11, 'y' => 11];
        
        // Ensure key and door are within grid
        $maxX = $this->level_config['grid_width'] - 1;
        $maxY = $this->level_config['grid_height'] - 1;
        
        $this->level_config['key']['x'] = max(0, min($maxX, (int)($this->level_config['key']['x'] ?? 0)));
        $this->level_config['key']['y'] = max(0, min($maxY, (int)($this->level_config['key']['y'] ?? 0)));
        $this->level_config['door']['x'] = max(0, min($maxX, (int)($this->level_config['door']['x'] ?? $maxX)));
        $this->level_config['door']['y'] = max(0, min($maxY, (int)($this->level_config['door']['y'] ?? $maxY)));
        
        // Prevent key and door from being in same spot
        if ($this->level_config['key']['x'] === $this->level_config['door']['x'] && 
            $this->level_config['key']['y'] === $this->level_config['door']['y']) {
            $this->level_config['door']['x'] = $maxX;
            $this->level_config['door']['y'] = $maxY;
        }
    }
    
    public function getLevelConfigJson(): string
    {
        return json_encode($this->level_config);
    }
}