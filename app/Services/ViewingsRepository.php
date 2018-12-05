<?php

namespace App\Services;

use App\Models\Viewing;
use App\Models\Property;
use App\User;
use Carbon\Carbon;

class ViewingsRepository
{
    protected $roomsRepository;
    
    public function __construct(RoomsRepository $roomsRepository)
    {
        $this->roomsRepository = $roomsRepository;
    }

    public function getAll(){
        return Viewing::with('property', 'property.images')->orderBy('date_time')->get();
    }

    public function getById($id){
        return Viewing::with('property', 'room', 'reservations')->find($id);
    }

    public function getPropertyViewings(Property $property){
        return $property->viewings()->with('property')->get();
    }

    public function createViewing(Property $property, $timestamp){
        $viewing = new Viewing([
            'date_time' => Carbon::parse($timestamp['date_time'])
        ]);
        $viewing->user_id = $property->user->id;
        $property->viewings()->save($viewing);

        $this->roomsRepository->createRoom($viewing);
        return Viewing::find($viewing->id);
    }

    public function deleteViewing(Viewing $viewing){
        $viewing->delete();
    }
}