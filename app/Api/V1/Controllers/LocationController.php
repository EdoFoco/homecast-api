<?php

namespace App\Api\V1\Controllers;

use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\GetAutocompleteRequest;
use App\Api\V1\Requests\GetPlaceRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\GooglePlacesClient;
use Dingo\Api\Routing\Helpers;

class LocationController extends Controller
{
    use Helpers;

    protected $googlePlacesClient;
    
    public function __construct(GooglePlacesClient $client)
    {
        $this->googlePlacesClient = $client;
    }

    public function getAutocomplete(GetAutoCompleteRequest $request)
    {
        $suggestions = $this->googlePlacesClient->autocomplete($request->input('type'), $request->input('input'));
        return $this->response->array($suggestions);
    }

    public function getPlace(GetPlaceRequest $request)
    {
        $place = $this->googlePlacesClient->getPlace($request->input('place_id'));
        if(!$place){
            throw new NotFoundHttpException();
        }
        return $this->response->array($place);
    }
}