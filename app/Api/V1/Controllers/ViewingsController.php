<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ViewingsRepository;
use App\Services\PropertiesRepository;
use App\Api\V1\Requests\ViewingRequest;
use Dingo\Api\Routing\Helpers;

class ViewingsController extends Controller
{
    use Helpers;
    protected $viewingsRepository;
    
    public function __construct(ViewingsRepository $viewingsRepository)
    {
        $this->viewingsRepository = $viewingsRepository;
    }

    public function getAll(){
        return response()
        ->json([
            'viewings' =>  $this->viewingsRepository->getAll()
        ]);
    }

    public function getViewing($id){
        $viewing = $this->viewingsRepository->getByid($id);
        if(!$viewing){
            throw new NotFoundHttpException("Viewing with id ".$id." was not found.");
        }

        return response()->json($viewing);
    }

    public function createViewing($propertyId, ViewingRequest $request, JWTAuth $JWTAuth){
        //Todo: Get Viewing by date, if exists throw conflict

        $user = $JWTAuth->toUser();
        $property = $user->properties()->find($propertyId);

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$propertyId." was not found or does not belong to this user.");
        }

        $viewing = $this->viewingsRepository->createViewing($property, $request->all());
        return response()->json($viewing);
    }

    public function deleteViewing($id){
        $viewing = $this->viewingsRepository->getByid($id);
        if(!$viewing){
            throw new NotFoundHttpException("Viewing with id ".$id." was not found.");
        }

        $this->viewingsRepository->deleteViewing($viewing);
        return $this->response()->noContent();
    }
}