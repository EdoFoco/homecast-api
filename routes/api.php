<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
    });

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        //Properties
        $api->get('properties', 'App\\Api\\V1\\Controllers\\PropertiesController@getAll');
        $api->post('properties', 'App\\Api\\V1\\Controllers\\PropertiesController@createProperty');
        $api->get('properties/{id}', 'App\\Api\\V1\\Controllers\\PropertiesController@getProperty');
        $api->delete('properties/{id}', 'App\\Api\\V1\\Controllers\\PropertiesController@deleteProperty');
        $api->get('properties/{id}/viewings', 'App\\Api\\V1\\Controllers\\ViewingsController@getAll');
        $api->post('properties/{id}/viewings', 'App\\Api\\V1\\Controllers\\ViewingsController@createViewing');
        
        //Viewings
        $api->get('viewings', 'App\\Api\\V1\\Controllers\\ViewingsController@getAll');
        $api->get('viewings/{id}', 'App\\Api\\V1\\Controllers\\ViewingsController@getViewing');
        $api->delete('viewings/{id}', 'App\\Api\\V1\\Controllers\\ViewingsController@deleteViewing');
        

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
