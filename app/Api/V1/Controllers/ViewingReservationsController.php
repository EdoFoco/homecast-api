<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Services\ViewingReservationsRepository;
use App\Services\ViewingsRepository;
use App\Services\UserRepository;
use App\Api\V1\Requests\ViewingReservationRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAuthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Dingo\Api\Routing\Helpers;

class ViewingReservationsController extends Controller
{
    use Helpers;
 
    protected $viewingsRepository;
    protected $viewingReservationsRepository;

    public function __construct(UserRepository $userRepository, ViewingReservationsRepository $viewingReservationsRepository, ViewingsRepository $viewingsRepository)
    {
        $this->viewingsRepository = $viewingsRepository;
        $this->viewingReservationsRepository = $viewingReservationsRepository;
    }

    public function getAll(JWTAuth $JWTAuth, $userId){
        $user = $JWTAuth->toUser();
        if($user->id != $userId){
            throw new UnauthorizedHttpException("Not authorized");
        }

        return response()
            ->json([
                'viewing_reservations' =>  $this->viewingReservationsRepository->getReservationsForUser($user)
            ]);
    }

    public function create(JWTAuth $JWTAuth, $userId, ViewingReservationRequest $request){
        $user = $JWTAuth->toUser();

        if($user->id != $userId){
            throw new UnauthorizedHttpException("Not authorized");
        }

        $viewing = $this->viewingsRepository->getById($request->input('viewing_id'));
        if(!$viewing){
            throw new NotFoundHttpException();
        }

        if($viewing->capacity <= 0){
            throw new ConflictHttpException('This viewing has no more capacity');
        }

        $viewingReservation = $this->viewingReservationsRepository->getReservationForUserByViewingId($user, $viewing->id);
        if($viewingReservation){
            throw new ConflictHttpException('User already made a reservation');
        }
        
        $this->viewingReservationsRepository->createReservation($user, $viewing);
        
        return $this->response()
            ->created();
    }

    public function delete(JWTAuth $JWTAuth, $userId, $viewingId){
        
        $user = $JWTAuth->toUser();
        
        if($user->id != $userId){
            throw new UnauthorizedHttpException("Not authorized");
        }

        $viewingReservation = $this->viewingReservationsRepository->getReservationForUserById($user, $viewingId);
        if(!$viewingReservation){
            throw new NotFoundHttpException();
        }

        $this->viewingReservationsRepository->deleteReservation($viewingReservation);
        
        return $this->response()
            ->noContent();
    }
}