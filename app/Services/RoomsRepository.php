<?php

namespace App\Services;

use App\Models\Viewing;
use App\Models\Room;

class RoomsRepository
{
    public function getAll(){
        return Room::all();
    }

    public function getById($id){
        return Room::find($id);
    }

    public function createRoom(Viewing $viewing){
        $room = new Room();
        $room->unique_id = uniqid();
        $viewing->room()->save($room);
        return $room;
    }
}