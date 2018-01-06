<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

class ScrapersController extends Controller
{
    use Helpers;

    public function get(){
        $scrapers = [
            'scrapers' => [
                [
                    'url' => '/api/properties/zoopla',
                    'bgColor' => 'rgba(85, 0, 123, 1)',
                    'name' => 'Zoopla'
                ]
            ]
        ];

        return response()->json($scrapers);
   }
}