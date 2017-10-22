<?php

namespace App\Services;

use App\Models\Property;
use App\User;

class PropertiesRepository
{
    public function getAll(){
        return Property::with('user')->get();
    }

    public function getUserProperties(User $user){
        return Property::with('user')->where('user_id', '=', $user->id)->get();
    }

    public function getById($id){
        return Property::find($id);
    }

    public function createProperty(User $user, $propertyInfo){
        $property = new Property($propertyInfo);
        $user->properties()->save($property);

        return $property;
    }

    public function deleteProperty(Property $property){
        $property->delete();
    }
}