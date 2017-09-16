<?php

namespace App\Api\V1\Controllers;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginController extends Controller
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
                throw new AccessDeniedHttpException();
            }
            
            $user = $JWTAuth->toUser($token);
            
            return response()
            ->json([
                'user' =>  $user,
                'token' => $token
            ]);

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

       
    }
}
