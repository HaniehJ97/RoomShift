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
    public array $level_config;
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
    
    public function getLevelConfigJson(): string
    {
        return json_encode($this->level_config);
    }
}