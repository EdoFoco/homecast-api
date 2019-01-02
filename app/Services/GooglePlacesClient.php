<?php

namespace App\Services;

use Config;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class GooglePlacesClient
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com';
    protected $autoCompleteEndpoint = '/maps/api/place/autocomplete/json?&components=country:gb';
    protected $placeEndpoint = '/maps/api/place/details/json?placeid=';
    protected $nearbyPlaces = '/maps/api/place/nearbysearch/json?location=51.4992331,-0.0410495&radius=2000&type=movie_theater&key=AIzaSyBI8XPW03pYA9WiEKjUiJ4d0Phz6e8VZE8';
    
    public function __construct()
    {
        $this->apiKey = Config::get('services.googlePlaces.key');
    }

    public function autocomplete($type='geocode', $input){
        $url = $this->baseUrl.$this->autoCompleteEndpoint.'&key='.$this->apiKey.'&types='.$type;
        
        if($input){
            $url = $url.'&input='.$input;
        }

        $client = new Client();
        $res = $client->get($url);
        $suggestions = json_decode($res->getBody());
        
        $result = [];
        $result['suggestions'] = [];

        if($suggestions->status == 'ZERO_RESULTS'){
            return $result;
        }
        
        if($suggestions->status != 'OK'){
            Log::error('Error while calling Google Places. Url: '.$url.' StatusCode:'.$suggestions->status);
            Log::error(json_encode($suggestions));
            return $result;
        }

        foreach($suggestions->predictions as $suggestion){
            $tmpAddress = [];
            $tmpAddress['description'] = $suggestion->description;
            $tmpAddress['place_id'] = $suggestion->place_id;

            array_push($result['suggestions'], $tmpAddress);
        }

        // $tmpAddress = [];
        // $tmpAddress['description'] = "55 Blue Anchor";
        // $tmpAddress['place_id'] = "dlksajfajsfoi";
        array_push($result['suggestions'], $tmpAddress);

        return $result;
    }

    public function getPlace($placeId){
        $url = $this->baseUrl.$this->placeEndpoint.urlencode($placeId).'&key='.$this->apiKey;
        $client = new Client();
        $res = $client->get($url);
        
        $place = json_decode($res->getBody());
        if($place->status != 'OK'){
            Log::error('Error while calling Google Places. Url: '.$url);
            return null;
        }
       
        $result = [];
        $result['postcode'] = '';
        foreach($place->result->address_components as $component){
            if(in_array('postal_code', $component->types)){
                $result['postcode'] = $component->short_name;
            }
        }

        $result['description'] = $place->result->formatted_address;
        $result['location'] = $place->result->geometry->location;
        return $result;
    }
}