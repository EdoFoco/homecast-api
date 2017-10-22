<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Favourite extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = [
        'user_id', 'property_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'user_id', 'property_id', 
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }
}