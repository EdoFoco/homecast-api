<?php

namespace App\Services;

use App\Models\Property;
use App\Models\DescriptionSection;
use App\Models\PropertyImage;
use App\User;
use Storage;
use DB;
use Log;

class PropertiesRepository
{
    
    public function getAll($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter){
        $query = $this->buildPropertiesQuery($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter); 
        return $query->get();
    }

    public function getUserProperties(User $user){
        return Property::with('user', 'descriptionSections', 'viewings', 'images')->where('user_id', '=', $user->id)->get();
    }

    public function getById($id){
        return Property::with('user', 'descriptionSections', 'viewings', 'images')->find($id);
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

    public function addPhotoToProperty($property, $photo){
        $path = Storage::putFile('property-'.$property->id, $photo, 'public');
        $path = Storage::url($path);

        PropertyImage::create([
            'property_id' => $property->id,
            'url' => $path
        ]);
    }

    public function deletePhotoFromProperty($property, $image){
        $path = $this->getPhotoPath($image->url);
        Storage::delete($path);
        PropertyImage::destroy($image->id);
    }

    private function getPhotoPath($url){
        $paths = explode('/', $url);
        return $paths[count($paths) - 2].'/'.$paths[count($paths) - 1];
    }

    private function getDistanceQuery($lat, $lng, $max_distance, $radius){
        return "id, name, address, google_place_id, latitude, longitude, user_id, 
            thumbnail, price, bedrooms, living_rooms, bathrooms, type, minimum_rental_period, listing_active, ( 
            {$radius} * acos( 
                cos( radians(  {$lat}  ) ) *
                cos( radians( latitude ) ) * 
                cos( radians( longitude ) - radians({$lng}) ) + 
                sin( radians(  {$lat}  ) ) *
                sin( radians( latitude ) ) 
                )
            ) AS distance";
    }

    private function buildPropertiesQuery($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter){
        $radius = 3959;
        $maxDistance = $maxDistanceFilter ? $maxDistanceFilter : 20;
        
        $query = Property::with('user', 'viewings', 'descriptionSections', 'images');
        
        if($coordinatesFilter){
            $distanceQuery = $this->getDistanceQuery($coordinatesFilter->lat, $coordinatesFilter->lng, $maxDistance, $radius);
            
            $query->select(DB::raw($distanceQuery))
                ->having("distance", "<", "{$maxDistance}")
                ->orderBy("distance", 'asc');
        }

        if($bedroomsFilter){
            $query->where('bedrooms', '=', $bedroomsFilter);
        }

        if($bathroomsFilter){
            $query->where('bathrooms', '=', $bathroomsFilter);
        }

        if($minPriceFilter){
            $query->where('price', '>=', $minPriceFilter);
        }

        if($maxPriceFilter){
            $query->where('price', '<=', $maxPriceFilter);
        }

        return $query->where('listing_active', '=', true);
    }
}