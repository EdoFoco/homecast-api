<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class ViewingRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.viewing.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
