<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class ZooplaPropertyRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.zooplaProperty.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
