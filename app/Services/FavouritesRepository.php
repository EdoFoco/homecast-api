<?php

namespace App\Services;

use App\Models\Favourite;

class FavouritesRepository
{
    protected $viewingsRepository;

    public function __construct(ViewingsRepository $viewingsRepository){
        $this->viewingsRepository = $viewingsRepository;
    }

    public function addFavourite($userId, $propertyId){
        $favourite = new Favourite([
            'user_id' => $userId,
            'property_id' => $propertyId
        ]);

        $favourite->save();
    }

    public function getFavourites($userId){
        $favourites = Favourite::with('property')->where('user_id', '=', $userId)->get();
        foreach($favourites as $favourite){
            $favourite->property['next_viewing'] = $this->viewingsRepository->getNextViewing($favourite->property);
        }
        return $favourites;
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