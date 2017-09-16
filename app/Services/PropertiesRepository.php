<?php

namespace App\Services;

use App\Models\Property;
use App\User;

class PropertiesRepository
{
    public function getAll(){
        return Property::all();
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