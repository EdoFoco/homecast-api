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
        //Users
        $api->get('users/me', 'App\\Api\\V1\\Controllers\\UsersController@getLoggedInUser');
        $api->get('users/{id}/properties', 'App\\Api\\V1\\Controllers\\PropertiesController@getUserProperties');
        $api->post('users/{id}/token', 'App\\Api\\V1\\Controllers\\UsersController@addDeviceToken');
        
        //Favourites
        $api->get('users/{id}/favourites', 'App\\Api\\V1\\Controllers\\FavouritesController@getFavourites');
        $api->post('users/{id}/favourites', 'App\\Api\\V1\\Controllers\\FavouritesController@addFavourite');
        $api->delete('users/{id}/favourites', 'App\\Api\\V1\\Controllers\\FavouritesController@deleteFavourite');
        
        //Properties
        $api->get('properties', 'App\\Api\\V1\\Controllers\\PropertiesController@getAll');
        $api->post('properties', 'App\\Api\\V1\\Controllers\\PropertiesController@createProperty');
        $api->post('properties/zoopla', 'App\\Api\\V1\\Controllers\\PropertiesController@createPropertyFromZoopla');
        $api->get('properties/{id}', 'App\\Api\\V1\\Controllers\\PropertiesController@getProperty');
        $api->put('properties/{id}', 'App\\Api\\V1\\Controllers\\PropertiesController@updateProperty');
        $api->post('properties/{id}/photos', 'App\\Api\\V1\\Controllers\\PropertiesController@uploadPhoto');
        $api->delete('properties/{id}/photos', 'App\\Api\\V1\\Controllers\\PropertiesController@deletePhotos');
        $api->delete('properties/{id}', 'App\\Api\\V1\\Controllers\\PropertiesController@deleteProperty');
        $api->get('properties/{id}/viewings', 'App\\Api\\V1\\Controllers\\ViewingsController@getPropertyViewings');
        $api->post('properties/{id}/viewings', 'App\\Api\\V1\\Controllers\\ViewingsController@createViewing');
        $api->post('properties/{id}/activation', 'App\\Api\\V1\\Controllers\\PropertiesController@activateProperty');

        //Viewings
        $api->get('viewings', 'App\\Api\\V1\\Controllers\\ViewingsController@getAll');
        $api->get('viewings/{id}', 'App\\Api\\V1\\Controllers\\ViewingsController@getViewing');
        $api->delete('viewings/{id}', 'App\\Api\\V1\\Controllers\\ViewingsController@cancelViewing');
        
        //Reserved Viewings
        $api->get('users/{id}/viewing-reservations', 'App\\Api\\V1\\Controllers\\ViewingReservationsController@getAll');
        $api->post('users/{id}/viewing-reservations', 'App\\Api\\V1\\Controllers\\ViewingReservationsController@create');
        $api->delete('users/{id}/viewing-reservations/{reservationId}', 'App\\Api\\V1\\Controllers\\ViewingReservationsController@delete');
        
        //Viewing Invitations
        $api->post('viewings/{id}/invitations', 'App\\Api\\V1\\Controllers\\ViewingInvitationsController@create');
        $api->get('users/{id}/invitations', 'App\\Api\\V1\\Controllers\\ViewingInvitationsController@getInvitations');


        //Scrapers
        $api->get('scrapers', 'App\\Api\\V1\\Controllers\\ScrapersController@get');
        
        //Location
        $api->get('autocomplete', 'App\\Api\\V1\\Controllers\\LocationController@getAutocomplete');
        $api->get('location', 'App\\Api\\V1\\Controllers\\LocationController@getGeocode');
        $api->get('place', 'App\\Api\\V1\\Controllers\\LocationController@getPlace');

        //Chat
        $api->post('chats', 'App\\Api\\V1\\Controllers\\ChatController@create');
        $api->get('chats', 'App\\Api\\V1\\Controllers\\ChatController@getUserChats');
        $api->post('chats/{id}/messages', 'App\\Api\\V1\\Controllers\\ChatController@createMessage');
        $api->get('chats/{id}/messages', 'App\\Api\\V1\\Controllers\\ChatController@getMessages');


        //JWT
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

    Route::get('/mailable', function () {
        $viewing = App\Models\Viewing::with('property')->find(15);
        $user = App\User::find(1);
        return (new App\Mail\ViewingInvitationMD('ciccio@ciccio.text', 'edo', $viewing, $user))->render();
        // return new App\Mail\ViewingInvitationEmail('edoardofire@hotmail.com', 'edoardofire@hotmail.com', $viewing);
    });

    Route::get('/redirect', function () {
        return view('redirect.redirect');
        // return new App\Mail\ViewingInvitationEmail('edoardofire@hotmail.com', 'edoardofire@hotmail.com', $viewing);
    });
});
