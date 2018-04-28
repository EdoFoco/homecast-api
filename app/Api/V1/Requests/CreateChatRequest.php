<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class CreateChatRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.createChatRequest.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
