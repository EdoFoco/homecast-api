<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.createMessageRequest.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
