<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class GetPlaceRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.getPlace.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
