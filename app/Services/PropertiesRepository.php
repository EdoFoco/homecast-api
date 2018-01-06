<?php

namespace App\Services;

use App\Models\Property;
use App\Models\DescriptionSection;
use App\User;

class PropertiesRepository
{
    public function getAll(){
        return Property::with('user', 'descriptionSections', 'viewings')->where('listing_active', '=', true)->get();
    }

    public function getUserProperties(User $user){
        return Property::with('user', 'descriptionSections', 'viewings')->where('user_id', '=', $user->id)->get();
    }

    public function getById($id){
        return Property::with('user', 'descriptionSections', 'viewings')->find($id);
    }

    public function createProperty(User $user, $propertyInfo){
        $property = new Property($propertyInfo);
        $user->properties()->save($property);

        foreach($propertyInfo['description_sections'] as $description){
            $property->descriptionSections()->save(new DescriptionSection($description));
        }

        return Property::with('descriptionSections', 'viewings')->find($property->id);
    }

    public function updateProperty(Property $property, $propertyInfo){
        
        $property->name = $propertyInfo['name'];
        $property->address = $propertyInfo['address'];
        $property->city = $propertyInfo['city'];
        $property->postcode = $propertyInfo['postcode'];
        $property->listing_active = $propertyInfo['listing_active'];
        $property->price = $propertyInfo['price'];
        $property->minimum_rental_period = $propertyInfo['minimum_rental_period'];
        $property->living_rooms = $propertyInfo['living_rooms'];
        $property->bedrooms = $propertyInfo['bedrooms'];
        $property->bathrooms = $propertyInfo['bathrooms'];
        $property->save();

        $descriptions = $property->descriptionSections;
        
        foreach($propertyInfo['description_sections'] as $description){
            
            if(!isset($description['id'])){
                $property->descriptionSections()->save(new DescriptionSection($description));
            }
            else{
                if(count($descriptions) > 0){
                    $existingDescription = $descriptions->find($description['id']);
                    if($existingDescription){
                        $existingDescription->title = $description['title'];
                        $existingDescription->description = $description['description'];
                        $existingDescription->save();
                    }
                }
            }
        }
        
        return Property::with('descriptionSections', 'viewings')->find($property->id);
    }

    public function deleteProperty(Property $property){
        $property->delete();
    }
}