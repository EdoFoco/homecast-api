<?php

namespace App\Api\V1\Controllers;

use Exception;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Services\PropertiesRepository;
use App\Services\UserRepository;
use App\Services\FavouritesRepository;
use App\Services\GooglePlacesClient;
use App\Services\ZooplaScraper;
use App\Api\V1\Requests\PropertyRequest;
use App\Api\V1\Requests\UpdatePropertyRequest;
use App\Api\V1\Requests\GetPropertiesRequest;
use App\Api\V1\Requests\ViewingRequest;
use App\Api\V1\Requests\ZooplaPropertyRequest;
use App\Api\V1\Requests\UploadPhotoRequest;
use App\Api\V1\Requests\PropertyActivationRequest;
use Dingo\Api\Routing\Helpers;

class PropertiesController extends Controller
{
    use Helpers;
    protected $propertiesRepository;
    protected $userRepository;
    protected $favouritesRepository;
    protected $zooplaScraper;
    protected $googlePlacesClient;
    
    public function __construct(PropertiesRepository $propertiesRepository, 
        UserRepository $userRepository, 
        FavouritesRepository $favouritesRepository, 
        ZooplaScraper $zooplaScraper,
        GooglePlacesClient $googlePlacesClient)
    {
        $this->propertiesRepository = $propertiesRepository;
        $this->userRepository = $userRepository;
        $this->favouritesRepository = $favouritesRepository;
        $this->zooplaScraper = $zooplaScraper;
        $this->googlePlacesClient = $googlePlacesClient;
    }

    public function getAll(GetPropertiesRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();

        $coordinatesFilter = null;
        $maxDistanceFilter = $request->input('max_distance');
        $bedroomsFilter = $request->input('bedrooms');
        $bathroomsFilter = $request->input('bathrooms');
        $minPriceFilter = $request->input('minPrice');
        $maxPriceFilter = $request->input('maxPrice');

        if($request->input('placeId')){
            $place = $this->googlePlacesClient->getPlace($request->input('placeId'));
            if(!$place){
                throw new NotFoundHttpException();
            }

            $coordinatesFilter = $place['location'];
        }

        $allProperties = $this->propertiesRepository->getAll($coordinatesFilter, $maxDistanceFilter, $bedroomsFilter, $bathroomsFilter, $minPriceFilter, $maxPriceFilter);

        $favourites = $this->favouritesRepository->getFavourites($user->id);

        $properties = [];
        foreach($allProperties as $property){
            if($favourites->contains('property_id', $property->id)){
                $property->isFavourite = true;
                array_push($properties, $property);
            }
            else{
                $property->isFavourite = false;
                array_push($properties, $property);
            }
        }

        return response()->json([
            'properties' => $properties
        ]);
    }

    public function getProperty(JWTAuth $JWTAuth, $id){
        $user = $JWTAuth->toUser();
        $property = $this->propertiesRepository->getByid($id);
        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        $favourites = $this->favouritesRepository->getFavourites($user->id);
        if($favourites->contains('property_id', $property->id)){
            $property->isFavourite = true;
        }
        else{
            $property->isFavourite = false;
        }

        return response()->json($property);
    }

    public function getUserProperties($userId){
        $user = $this->userRepository->getUser($userId);
        if(!$user){
            throw new NotFoundHttpException("User not found");
        }
        
        return response()->json([
            'properties' =>   $this->propertiesRepository->getUserProperties($user)
        ]);
    }

    public function createProperty(PropertyRequest $request, JWTAuth $JWTAuth){
        //Todo: Get existing property by user, postcode, date/time, if exists throw conflict
        $user = $JWTAuth->toUser();
        $property = $this->propertiesRepository->createProperty($user, $request->all());
        return $this->response()->created($property->id);
    }

    public function deleteProperty($id){
        $property = $this->propertiesRepository->getByid($id);
        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        $this->propertiesRepository->deleteProperty($property);
        return $this->response()->noContent();
    }

    public function updateProperty($id, UpdatePropertyRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $properties = $this->propertiesRepository->getUserProperties($user);
        $property = $properties->find($id);

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        $property = $this->propertiesRepository->updateProperty($property, $request->all());
        return response()->json($property);
    }

    public function activateProperty($id, PropertyActivationRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $properties = $this->propertiesRepository->getUserProperties($user);
        $property = $properties->find($id);

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        $activateProperty = $request->input('listing_active');
        if($activateProperty){
            try{
                $property = $this->propertiesRepository->activateProperty($property);
            }
            catch(Exception $e){
                throw new BadRequestHttpException($e->getMessage());
            }
        }
        else{
            $property = $this->propertiesRepository->deActivateProperty($property);
        }
        return $this->response()->noContent();
    }

    public function uploadPhoto($propertyId, UploadPhotoRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $properties = $this->propertiesRepository->getUserProperties($user);
        $property = $properties->find($propertyId);

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$propertyId." was not found.");
        }

        $image = $request->file('image');
        $this->propertiesRepository->addPhotoToProperty($property, $image);
        return $this->response()->noContent();
    }

    public function deletePhotos($propertyId, Request $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $properties = $this->propertiesRepository->getUserProperties($user);
        
        $photoIds = $request->query('ids');
        if(!$photoIds ){
            throw new BadRequestHttpException('Id\'s are required.');
        }

        $photoIds = explode(',', $photoIds);

        $property = $properties->find($propertyId);

        if($property->listing_active && count($photoIds) >= count($property->images)){
            throw new BadRequestHttpException('You cannot delete all images while the listing is active.');
        }

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$propertyId." was not found.");
        }

        $images = $property->images->find($photoIds);
        if(count($images) < 1){
            throw new NotFoundHttpException("Incorrect photo ids.");
        }

        $this->propertiesRepository->deletePhotosFromProperty($property, $images);
        return $this->response()->noContent();
    }

    public function createPropertyFromZoopla(ZooplaPropertyRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();

        $id = $request->input('property_id');
        $this->zooplaScraper->scrapeProperty($user, $id);
        return $this->response()->created();
    }
}