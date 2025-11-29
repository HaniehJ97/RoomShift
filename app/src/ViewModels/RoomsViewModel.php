<?php

namespace App\ViewModels;

use App\Models\RoomModel;

class RoomsViewModel
{
    /**
     * @var RoomModel[]
     */
    public array $rooms;

    public function __construct(array $rooms)
    {
        $this->rooms = $rooms;
    }
}