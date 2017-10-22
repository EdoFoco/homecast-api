<?php

namespace App\Services;

use App\Models\Favourite;

class FavouritesRepository
{
    public function addFavourite($userId, $propertyId){
        $favourite = new Favourite([
            'user_id' => $userId,
            'property_id' => $propertyId
        ]);

        $favourite->save();
    }

    public function getFavourites($userId){
        return Favourite::with('property')->where('user_id', '=', $userId)->get();
    }

    public function getFavourite($userId, $propertyId){
        return Favourite::where('user_id', '=', $userId)->where('property_id', '=', $propertyId)->first();
    }

    public function getFavouriteById($favouriteId){
        return Favourite::find($favouriteId);
    }

    public function removeFavourite(Favourite $favourite){
        $favourite->delete();
    }
}