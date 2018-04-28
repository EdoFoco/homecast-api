<?php

return [

    'sign_up' => [
        'release_token' => env('SIGN_UP_RELEASE_TOKEN'),
        'validation_rules' => [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]
    ],

    'login' => [
        'validation_rules' => [
            'email' => 'required|email',
            'password' => 'required'
        ]
    ],

    'forgot_password' => [
        'validation_rules' => [
            'email' => 'required|email'
        ]
    ],

    'reset_password' => [
        'release_token' => env('PASSWORD_RESET_RELEASE_TOKEN', false),
        'validation_rules' => [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]
    ],
    'property' => [
        'validation_rules' => [
            'name' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'city' => 'required',
            'price' => 'required | between:0,9999999.99',
            'description_sections' => 'required | array',
            'bedrooms' => 'required | integer',
            'bathrooms' => 'required | integer',
            'living_rooms' => 'required | integer',
            'type' => 'required',
            'minimum_rental_period' => 'integer'
        ]
    ],

    'viewing' => [
        'validation_rules' => [
            'date_time' => 'required | date'
        ]
    ],

    'favourite' => [
        'validation_rules' => [
            'property_id' => 'required | integer'
        ]
    ],

    'viewingReservation' => [
        'validation_rules' => [
            "viewing_id" => 'required'
        ]
    ],

    'viewingInvitation' => [
        'validation_rules' => [
            "user_email" => 'email'
        ]
    ],

    'zooplaProperty' => [
        'validation_rules' => [
            "property_id" => 'required'
        ]
    ],

    'getPlace' => [
        'validation_rules' => [
            "place_id" => 'required'
        ]
    ],

    'getPropertiesRequest' => [
        'validation_rules' => [
            'max_distance' => 'integer',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'minPrice' => 'integer',
            'maxPrice' => 'integer',
        ]
    ],

    'uploadPhotoRequest' => [
        'validation_rules' => [
            "image" => 'required | image'
        ]
    ],

    'createChatRequest' => [
        'validation_rules' => [
            "participants" => 'required | array',
            'participants.*' => 'integer'
        ]
    ],

    'createMessageRequest' => [
        'validation_rules' => [
            "message" => 'required | string',
        ]
    ],

];
