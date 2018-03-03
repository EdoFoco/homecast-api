<?php

namespace App\Api\V1\Controllers;

use Config;
use App\User;
use Dingo\Api\Routing\Helpers;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\SignUpRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use App\Services\UserRepository;
use App\Services\ViewingInvitationsRepository;


class SignUpController extends Controller
{
    protected $usersRepository;
    protected $viewingInvitationsRepository;

    public function __construct(UserRepository $usersRepository, ViewingInvitationsRepository $viewingInvitationsRepository)
    {
        $this->usersRepository = $usersRepository;
        $this->viewingInvitationsRepository = $viewingInvitationsRepository;
    }

    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
      
        $existingUser = $this->usersRepository->getByEmail($request->input('email'));
        if($existingUser){
            throw new ConflictHttpException("A user with this email already exists.");
        }

        $user = $this->usersRepository->saveUser($request->all());
     
        // $invitations = $this->viewingInvitationsRepository->getByEmail($request->input('user_email'));
        // foreach($invitations as $invitation){
        //     if(!$invitation->user_id){
        //         $this->viewingInvitationsRepository->updateWithUserId($invitation, $user->id);
        //     }
        // }

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'user' => $user
            ], 201);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
