<?php

namespace App\Services;

use App\Models\Viewing;
use App\Models\Property;
use App\User;

class ViewingsRepository
{
    protected $roomsRepository;
    
    public function __construct(RoomsRepository $roomsRepository)
    {
        $this->roomsRepository = $roomsRepository;
    }

    public function getAll(){
        return Viewing::with('property')->orderBy('date_time')->get();
    }

    public function getById($id){
        return Viewing::with('property', 'room')->find($id);
    }

    public function createViewing(Property $property, $timestamp){
        $viewing = new Viewing($timestamp);
        $viewing->user_id = $property->user->id;
        $property->viewings()->save($viewing);

        $this->roomsRepository->createRoom($viewing);
        return $viewing;
    }

    public function deleteViewing(Viewing $viewing){
        $viewing->delete();
    }
}