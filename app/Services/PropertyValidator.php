<?php

namespace App\Services;

use Exception;

class PropertyValidator
{
    public function validateActivationDetails($property){
        if(count($property->images) < 1)
            throw new Exception("We need at least one image for this property.");
        
        if(!isset($property->address) 
        || !isset($property->postcode) 
        || !isset($property->latitude)
        || !isset($property->longitude)
        || !isset($property->google_place_id))
        {
            throw new Exception("The address seems to be incorrect.");
        }
        
        if(!isset($property->description))
            throw new Exception("You forgot to write a description.");
        
        if(!isset($property->price) || $property->price <= 0)
            throw new Exception("Please insert the monthly cost.");
        
        if(!isset($property->living_rooms) || $property->living_rooms <= 0)
            throw new Exception("Please insert the number of living rooms.");
    
        if(!isset($property->bedrooms) || $property->bedrooms <= 0)
            throw new Exception("Please insert the number of bedrooms.");
        
        if(!isset($property->bathrooms) || $property->bathrooms <= 0)
            throw new Exception("Please insert the number of bathrooms.");
    }
}