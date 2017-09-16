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
            'price' => 'required | between:0,999999.99',
            'description' => 'required'
        ]
    ],

    'viewing' => [
        'validation_rules' => [
            'date_time' => 'required | date'
        ]
    ]

];
