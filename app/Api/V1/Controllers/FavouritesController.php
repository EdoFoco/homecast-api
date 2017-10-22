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
use App\Api\V1\Requests\FavouriteRequest;
use Dingo\Api\Routing\Helpers;

class FavouritesController extends Controller
{
    use Helpers;

    protected $propertiesRepository;
    protected $userRepository;
    protected $favouritesRepository;
    
    public function __construct(PropertiesRepository $propertiesRepository, UserRepository $userRepository, FavouritesRepository $favouritesRepository)
    {
        $this->propertiesRepository = $propertiesRepository;
        $this->userRepository = $userRepository;
        $this->favouritesRepository = $favouritesRepository;
    }

    public function addFavourite(FavouriteRequest $request, JWTAuth $JWTAuth, $userId){
        $user = $JWTAuth->toUser();
        if($user->id != $userId){
            throw new UnauthorizedHttpException("Unauthorized");
        }
        
        $propertyId = $request->input('property_id');
        $property = $this->propertiesRepository->getById($propertyId);
        if(!$property){
            throw new NotFoundHttpException("Property not found");
        }

        $favourite = $this->favouritesRepository->getFavourite($userId, $propertyId);
        if($favourite){
            throw new ConflictHttpException();
        }

        $this->favouritesRepository->addFavourite($userId, $propertyId);
        return $this->response->created();
   }

   public function getFavourites(JWTAuth $JWTAuth, $userId){
        $user = $JWTAuth->toUser();
        if($user->id != $userId){
            throw new UnauthorizedHttpException("Unauthorized");
        }

        $favourites = $this->favouritesRepository->getFavourites($userId);
        return response()->json([
            'favourites' => $favourites
        ]);
   }

   public function deleteFavourite(FavouriteRequest $request, JWTAuth $JWTAuth, $userId){
        $user = $JWTAuth->toUser();
        if($user->id != $userId){
            throw new UnauthorizedHttpException("Unauthorized");
        }

        $favourite = $this->favouritesRepository->getFavourite($userId, $request->input('property_id'));
        if(!$favourite){
            throw new NotFoundHttpException();
        }

        $this->favouritesRepository->removeFavourite($favourite);
        return $this->response->noContent();
   }
}