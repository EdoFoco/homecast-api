<?php

namespace App\Services;

use App\User;

class UserRepository
{
    public function getAll(){
       return User::all();
    }

    public function getByEmail($email){
        return User::where('email', '=', $email)->first();
    }

    public function getUser($userId){
        return User::find($userId);
    }

    public function saveUser($userInfo){
        $user = new User($userInfo);
        $user->save();
        return $user;
    }
}