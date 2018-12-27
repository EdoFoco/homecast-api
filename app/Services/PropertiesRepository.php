<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyImage;
use App\User;
use Carbon\Carbon;
use App\Services\PropertyValidator;
use Storage;
use DB;
use Log;

class PropertiesRepository
{
    protected $googlePlacesClient;
    protected $propertyValidator;
    protected $viewingsRepository;

    public function __construct(GooglePlacesClient $googlePlacesClient, PropertyValidator $propertyValidator, ViewingsRepository $viewingsRepository){
        $this->googlePlacesClient = $googlePlacesClient;
        $this->propertyValidator = $propertyValidator;
        $this->viewingsRepository = $viewingsRepository;
    }

    public function getAll($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter){
        return $this->getAllWithFilters($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter); 
    }

    public function getUserProperties(User $user){
        return Property::with(['user:id', 'images', 'viewings' => function ($q) {
            $q->with('status')
            ->whereDate('date_time', '>=', Carbon::now()->subDays(1))
            ->orderByDesc('date_time');
         }])
         ->where('user_id', '=', $user->id)
         ->get();
    }

    public function getById($id){
        $property = Property::with('user:id', 'images')->find($id);
        $property['nextViewing'] = $this->viewingsRepository->getNextViewing($property);;
        return $property;
    }

    public function createProperty(User $user, $propertyInfo){
        $place = $this->googlePlacesClient->getPlace($propertyInfo['google_place_id']);
        $propertyInfo['latitude'] = $place['location']->lat;
        $propertyInfo['longitude'] = $place['location']->lng;
        $propertyInfo['address'] = $place['description'];
        $propertyInfo['postcode'] = $place['postcode'];
        
        $property = new Property($propertyInfo);

        $user->properties()->save($property);

        return Property::with('viewings')->find($property->id);
    }

    public function updateProperty(Property $property, $propertyInfo){
        if(isset($propertyInfo['google_place_id']) && $propertyInfo['google_place_id'] != $property->google_place_id){
            $place = $this->googlePlacesClient->getPlace($propertyInfo['google_place_id']);
            $property['address'] = $place['description'];
            $property->postcode = $place['postcode'];
            $property->latitude = $place['location']->lat;
            $property->longitude = $place['location']->lng;
            $property->google_place_id = $propertyInfo['google_place_id'];
        }
       
        if(isset($propertyInfo['description']))
            $property->description = $propertyInfo['description'];
        
        if(isset($propertyInfo['price']))
            $property->price = $propertyInfo['price'];
        
        if(isset($propertyInfo['living_rooms']))
            $property->living_rooms = $propertyInfo['living_rooms'];
      
        if(isset($propertyInfo['bedrooms']))
            $property->bedrooms = $propertyInfo['bedrooms'];
        
        if(isset($propertyInfo['bathrooms']))
            $property->bathrooms = $propertyInfo['bathrooms'];
        $property->save();

        return Property::with('viewings')->find($property->id);
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

    public function deletePhotosFromProperty($property, $images){
        foreach($images as $image){
            $path = $this->getPhotoPath($image->url);
            Storage::delete($path);
            PropertyImage::destroy($image->id);
        }
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

    private function getAllWithFilters($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter){
        $radius = 3959;
        $maxDistance = $maxDistanceFilter ? $maxDistanceFilter : 20;
        $query = Property::with('user:id', 'images', 'viewings', 'viewings.status');
        
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

        $properties = $query->where('listing_active', '=', true)->get();
        foreach($properties as $property){
            $nextViewing = $this->viewingsRepository->getNextViewing($property);
            $property['nextViewing'] = $nextViewing;
        }

        return $properties;
    }

    public function activateProperty($property){
        $this->propertyValidator->validateActivationDetails($property);
        $property->listing_active = true;
        $property->save();
    }

    public function deActivateProperty($property){
        //Todo: Cancel all viewings
        foreach($property->viewings as $viewing){
            $this->viewingsRepository->cancelViewing($viewing);
        }
        $property->listing_active = false;
        $property->save();
    }
}