<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Services\UserRepository;
use App\Api\V1\Requests\FavouriteRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Dingo\Api\Routing\Helpers;

class UsersController extends Controller
{
    use Helpers;

    protected $userRepository;
   
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getLoggedInUser(JWTAuth $JWTAuth){
        $token = $JWTAuth->getToken()->get();
       
        return response()
        ->json([
            'user' =>  $JWTAuth->toUser(),
            'token' => $token->get()
        ]);
    }
   
}