<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'address', 
        'postcode',
        'google_place_id', 
        'latitude', 
        'longitude',
        'user_id', 
        'thumbnail', 
        'price', 
        'bedrooms', 
        'living_rooms', 
        'bathrooms',
        'listing_active'
    ];

    protected $casts = [
        'listing_active' => 'boolean',
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'user_id'
    ];

    public function viewings()
    {
        return $this->hasMany('App\Models\Viewing');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function favourites(){
        return $this->hasMany('App\Models\Favourite');
    }

    public function images(){
        return $this->hasMany('App\Models\PropertyImage');
    }
}