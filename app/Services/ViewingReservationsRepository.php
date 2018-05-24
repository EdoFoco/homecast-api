<?php

namespace App\Services;

use App\Models\ViewingReservation;
use App\Models\Viewing;
use App\User;

class ViewingReservationsRepository
{
    public function getReservationsForUser(User $user){
        return $user->viewingReservations()->with('viewing', 'viewing.property', 'viewing.property.images')->get();
    }

    public function getReservationForUserById(User $user, $reservationId){
        return $user->viewingReservations()->find($reservationId);
    }

    public function getReservationForUserByViewingId(User $user, $viewingId){
        return ViewingReservation::where('user_id', '=', $user->id)->where('viewing_id', '=', $viewingId)->first();
    }

    public function createReservation(User $user, Viewing $viewing){
        $viewing->capacity -= 1;
        $viewing->save();
       
       return $user->viewingReservations()->save(new ViewingReservation([
            'user_id' => $user->id,
            'viewing_id' => $viewing->id
        ]));
    }

    public function deleteReservation(ViewingReservation $viewingReservation){
        $viewing = $viewingReservation->viewing;
        if($viewing->capacity < 10)
        {
            $viewing->capacity += 1;
            $viewing->save();
        } 

        $viewingReservation->delete();
    }
}