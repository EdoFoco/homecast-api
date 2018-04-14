<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.uploadPhotoRequest.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
