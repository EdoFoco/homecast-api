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

    public function findMany($ids){
        return User::findMany($ids);
    }

    public function addDeviceToken($user, $token){
        $user->device_token = $token;
        $user->save();
    }
}