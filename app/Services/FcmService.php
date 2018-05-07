<?php

namespace App\Services;

use Config;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions as GuzzleOptions;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;

class FcmService {

    protected $baseUrl;
    protected $authToken;

    public function __constructor(){
        $this->baseUrl = 'https://fcm.googleapis.com';//Config::get('broadcasting.connections.fcm.url');
        $this->authToken = 'key=AAAA2pzJfmw:APA91bFdiuTpgsao2RlYZjUxcg4qQZU7Vtpr2ZpUxwMR0V-vDA3lGV_lIsnNfmU-PXjRJVCOBrb2Zx3u_Om2e-xdtBcbTkh_rwivzqi6cFDXabjE6YBru9joEM_oOl9qpKJqzNlAwXS9'; //Config::get('broadcasting.connections.fcm.auth_token');
    }

    public function sendNotification($title, $message, $token, $data){
        $this->baseUrl = 'https://fcm.googleapis.com';//Config::get('broadcasting.connections.fcm.url');//'//
        $this->authToken = 'key=AAAA2pzJfmw:APA91bFdiuTpgsao2RlYZjUxcg4qQZU7Vtpr2ZpUxwMR0V-vDA3lGV_lIsnNfmU-PXjRJVCOBrb2Zx3u_Om2e-xdtBcbTkh_rwivzqi6cFDXabjE6YBru9joEM_oOl9qpKJqzNlAwXS9'; //Config::get('broadcasting.connections.fcm.auth_token');
  
        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $message
            ],
            'data' => $data
        ];

        Log::Error(json_encode($payload));
        Log::Error($this->baseUrl);
        Log::Error($this->authToken);

        $client = new Client();
         try {
            $res = $client->request('POST', $this->baseUrl.'/fcm/send', [
                GuzzleOptions::JSON => $payload,
               'headers' => [ 'Authorization' => $this->authToken ]
           ]);
        } catch (RequestException $requestException) {
            Log::Error($requestException);
        }
       
    }
}