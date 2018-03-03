<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Services\ViewingReservationsRepository;
use App\Services\ViewingsRepository;
use App\Services\ViewingInvitationsRepository;
use App\Services\UserRepository;
use App\Api\V1\Requests\ViewingInvitationRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAuthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Dingo\Api\Routing\Helpers;
use App\Mail\ViewingInvitationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ViewingInvitationsController extends Controller
{
    use Helpers;
 
    protected $viewingsRepository;
    protected $viewingReservationsRepository;
    protected $viewingInvitationsRepository;
    protected $usersRepository;

    public function __construct(UserRepository $usersRepository, 
            ViewingReservationsRepository $viewingReservationsRepository, 
            ViewingsRepository $viewingsRepository,
            ViewingInvitationsRepository $viewingInvitationsRepository)
    {
        $this->viewingsRepository = $viewingsRepository;
        $this->viewingReservationsRepository = $viewingReservationsRepository;
        $this->usersRepository = $usersRepository;
        $this->viewingInvitationsRepository = $viewingInvitationsRepository;
    }

    public function create(JWTAuth $JWTAuth, $viewingId, ViewingInvitationRequest $request){
        $user = $JWTAuth->toUser();

        $properties = $user->properties;
        if(!$properties){
            throw new NotFoundHttpException("Resource not found");
        }
        if(count($properties) == 0){
            throw new NotFoundHttpException("Resource not found");
        }

        $propertyIds = $properties->pluck('id')->toArray();
        $viewing = $this->viewingsRepository->getById($viewingId);
      
        if(!$viewing || !in_array($viewing->property->id, $propertyIds)){
            throw new UnauthorizedHttpException("Not authorized");
        }

        $existingUser = $this->usersRepository->getByEmail($request);
        $existingInvitation = $this->viewingInvitationsRepository
                ->getByViewingAndEmail($viewingId, $request
                ->input('user_email'));
        
        if($existingInvitation){
            throw new ConflictHttpException("Viewing invitation already exists");
        }

        if($request->input('user_id')){
            $invitation = $this->viewingInvitationsRepository->create($viewingId, $request->input('user_id'), null);
        }
        else{
            $existingUser = $this->usersRepository->getByEmail($request->input('user_email'));
            if($existingUser){
                $invitation = $this->viewingInvitationsRepository->create($viewingId, $existingUser->id, $request->input('user_email'));
            }
            else{
                $invitation = $this->viewingInvitationsRepository->create($viewingId, null, $request->input('user_email'));
            }
    
            try{
                Mail::queue(new ViewingInvitationEmail($request->input('user_email'), 'Edoardo', $viewing));
            }
            catch(Exception $e){
                Log::debug($e);
            }
        }

        return $this->response()->created();
    }

    public function getInvitations(JWTAuth $JWTAuth){
        $user = $JWTAuth->toUser();

        return $user->viewingInvitations;
    }
}