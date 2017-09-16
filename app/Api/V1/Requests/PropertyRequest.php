<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.property.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
