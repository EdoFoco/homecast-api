<?php

namespace App\Api\V1\Requests;

use Config;
use Dingo\Api\Http\FormRequest;

class ViewingInvitationRequest extends FormRequest
{
    public function rules()
    {
        return Config::get('boilerplate.viewingInvitation.validation_rules');
    }

    public function authorize()
    {
        return true;
    }
}
