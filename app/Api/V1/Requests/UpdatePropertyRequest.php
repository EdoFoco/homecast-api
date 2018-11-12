<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.update_property.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
