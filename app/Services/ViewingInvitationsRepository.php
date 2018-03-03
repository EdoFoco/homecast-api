<?php

namespace App\Services;

use App\Models\ViewingInvitation;
use App\Models\Viewing;
use App\User;

class ViewingInvitationsRepository
{
    public function getByEmail($email){
        return ViewingInvitation::where('user_email', '=', $email)->get();
    }

    public function getByViewingAndEmail($viewingId, $userEmail){
        return ViewingInvitation::with('viewing')
            ->where('viewing_id', '=', $viewingId)
            ->where('user_email', '=', $userEmail)->first();
    }

    public function create($viewingId, $userId, $userEmail){
       return ViewingInvitation::create([
            'viewing_id' => $viewingId,
            'user_id' => $userId ? $userId : null,
            'user_email' => $userEmail ? $userEmail : null
        ]);
    }

    public function updateWithUserId($invitation, $userId){
        $invitation->user_id = $userId;
        $invitation->save();
    }

    public function delete($id){
       ViewingInvitation::delete($id);
    }
}