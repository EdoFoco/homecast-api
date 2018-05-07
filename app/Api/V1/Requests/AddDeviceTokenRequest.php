<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class AddDeviceTokenRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.addDeviceToken.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
