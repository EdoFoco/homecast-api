<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\PropertiesRepository;
use App\Api\V1\Requests\PropertyRequest;
use App\Api\V1\Requests\ViewingRequest;
use Dingo\Api\Routing\Helpers;

class PropertiesController extends Controller
{
    use Helpers;
    protected $propertiesRepository;
    
    public function __construct(PropertiesRepository $propertiesRepository)
    {
        $this->propertiesRepository = $propertiesRepository;
    }

    public function getAll(){
        return response()
        ->json([
            'properties' =>  $this->propertiesRepository->getAll()
        ]);
    }

    public function getProperty($id){
        $property = $this->propertiesRepository->getByid($id);
        if(!$property){
            throw new NotFoundHttpException("Property with id ".$id." was not found.");
        }

        return response()->json($property);
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
}