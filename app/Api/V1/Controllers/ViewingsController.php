<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\ViewingsRepository;
use App\Services\PropertiesRepository;
use App\Services\ViewingReservationsRepository;
use App\Api\V1\Requests\ViewingRequest;
use Dingo\Api\Routing\Helpers;

class ViewingsController extends Controller
{
    use Helpers;
    protected $viewingsRepository;
    protected $viewingReservationsRepository;
    protected $propertiesRepository;

    public function __construct(ViewingsRepository $viewingsRepository, ViewingReservationsRepository $viewingReservationsRepository, PropertiesRepository $propertiesRepository)
    {
        $this->viewingsRepository = $viewingsRepository;
        $this->viewingReservationsRepository = $viewingReservationsRepository;
        $this->propertiesRepository = $propertiesRepository;
    }

    public function getAll(){
        return response()
        ->json([
            'viewings' =>  $this->viewingsRepository->getAll()
        ]);
    }

    public function getPropertyViewings($propertyId){
        $property = $this->propertiesRepository->getById($propertyId);
        if(!$property){
            throw new NotFoundException("Property not found.");
        }

        return response()->json([
            'viewings' => $this->viewingsRepository->getPropertyViewings($property)
        ]);
    }

    public function getViewing(JWTAuth $JWTAuth, $id){
        $user = $JWTAuth->toUser();

        $viewing = $this->viewingsRepository->getByid($id);
        if(!$viewing){
            throw new NotFoundHttpException("Viewing with id ".$id." was not found.");
        }

        $viewingReservation = $this->viewingReservationsRepository->getReservationForUserByViewingId($user, $viewing->id);
        if($viewingReservation){
            $viewing->viewing_reservation = [
                'id' => $viewingReservation->id
            ];
        }

        return response()->json($viewing);
    }

    public function createViewing($propertyId, ViewingRequest $request, JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();
        $property = $user->properties()->find($propertyId);

        if(!$property){
            throw new NotFoundHttpException("Property with id ".$propertyId." was not found or does not belong to this user.");
        }

        $viewing = $this->viewingsRepository->createViewing($property, $request->all());
        return response()->json($viewing, 201);
    }

    public function cancelViewing($id){
        $viewing = $this->viewingsRepository->getByid($id);
        if(!$viewing){
            throw new NotFoundHttpException("Viewing with id ".$id." was not found.");
        }

        $this->viewingsRepository->cancelViewing($viewing);
        return $this->response()->noContent();
    }
}