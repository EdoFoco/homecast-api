<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\PropertiesRepository;
use App\Services\UserRepository;
use App\Services\FavouritesRepository;
use App\Services\ZooplaScraper;
use App\Api\V1\Requests\PropertyRequest;
use App\Api\V1\Requests\ViewingRequest;
use App\Api\V1\Requests\ZooplaPropertyRequest;
use Dingo\Api\Routing\Helpers;

class PropertiesController extends Controller
{
    use Helpers;
    protected $propertiesRepository;
    protected $userRepository;
    protected $favouritesRepository;
    protected $zooplaScraper;
    
    public function __construct(PropertiesRepository $propertiesRepository, UserRepository $userRepository, FavouritesRepository $favouritesRepository, ZooplaScraper $zooplaScraper)
    {
        $this->propertiesRepository = $propertiesRepository;
        $this->userRepository = $userRepository;
        $this->favouritesRepository = $favouritesRepository;
        $this->zooplaScraper = $zooplaScraper;
    }

    public function getAll(JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $allProperties =  $this->propertiesRepository->getAll();
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
        //Todo: Get existing viewing by user, postcode, date/time, if exists throw conflict
        $user = $JWTAuth->toUser();
        $property = $this->propertiesRepository->createProperty($user, $request->all());
        return response()->json($property);
    }

    public function deleteProperty($id){
        $property = $this->propertiesRepository->getByid($id);
        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        $this->propertiesRepository->deleteProperty($property);
        return $this->response()->noContent();
    }

    public function updateProperty($id, PropertyRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $properties = $this->propertiesRepository->getUserProperties($user);
        $property = $properties->find($id);

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        $property = $this->propertiesRepository->updateProperty($property, $request);
        return response()->json($property);
    }

    public function createPropertyFromZoopla(ZooplaPropertyRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();

        $id = $request->input('property_id');
        $this->zooplaScraper->scrapeProperty($user, $id);
    }
}