<?php

namespace App\Controllers;

use App\Models\RoomModel;
use App\Services\IRoomService;
use App\ViewModels\RoomsViewModel;

class RoomController
{
    private IRoomService $roomService;

    public function __construct()
    {
        $this->roomService = new \App\Services\RoomService();
    }

    public function index(array $vars = []): void
    {
        $rooms = $this->roomService->getAllRooms();
        $vm = new RoomsViewModel($rooms);
        
        require __DIR__ . '/../Views/rooms/index.php';
    }

    public function store(array $vars = []): void
    {
        try {
            $room = new RoomModel($_POST);
            $this->roomService->createRoom($room);
            
            header('Location: /rooms');
            exit;
            
        } catch (\InvalidArgumentException $e) {
            $rooms = $this->roomService->getAllRooms();
            $vm = new RoomsViewModel($rooms);
            $error = $e->getMessage();
            
            require __DIR__ . '/../Views/rooms/index.php';
        }
    }
}