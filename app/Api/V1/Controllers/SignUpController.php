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
//use App\Services\UserRepository;


class SignUpController extends Controller
{
    //Todo: Use usersRepository
    /*protected $usersRepository;
    
    public function __construct(UserRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }*/

    public function signUp(SignUpRequest $request, JWTAuth $JWTAuth)
    {
      
        /*$existingUser = $this->usersRepository->getByEmail($request->input('email'));
        
        if($existingUser){
            throw new ConflictHttpException("A user with this email already exists.");
        }

        $user = $this->usersRepository->saveUser($request->all());
     */
        $user = new User($request->all());
        $user->save();
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
