<?php

namespace App\Services;

use App\Models\Viewing;
use App\Models\Property;
use App\Models\ViewingStatus;
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
        return Viewing::with('status', 'property', 'property.images')
            ->whereDate('date_time', '>=', Carbon::now()->subDays(1))
            ->orderBy('date_time')
            ->get();
    }

    public function getById($id){
        return Viewing::with('status', 'property', 'room', 'reservations')->find($id);
    }

    public function getPropertyViewings(Property $property){
        return $property->viewings()->with('status', 'property')->get();
    }
 
    public function createViewing(Property $property, $timestamp){
        $statusId = ViewingStatus::where('status', '=', 'ACTIVE')->first()->id;
        $viewing = new Viewing([
            'date_time' => Carbon::parse($timestamp['date_time'])
        ]);

        $viewing->user_id = $property->user->id;
        $viewing->viewing_status_id = $statusId;
        $property->viewings()->save($viewing);

        $this->roomsRepository->createRoom($viewing);
        return Viewing::find($viewing->id);
    }

    public function cancelViewing(Viewing $viewing){
        //Todo: Send an email to all viewing invitations saying the viewing was cancelled
        $status = ViewingStatus::where('status', '=', 'CANCELLED')->first();
        $viewing->viewing_status_id = $status->id;
        $viewing->save();
    }
}