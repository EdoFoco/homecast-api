<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Viewing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_time', 'user_id', 'property_id', 'capacity', 'isLive'
    ];

    protected $casts = [
        'isLive' => 'boolean',
        //'date_time' => 'datetime'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
   
    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'property_id'
    ];

   
    public function getDateTimeAttribute($dateTime) {
        return Carbon::parse($dateTime)->toIso8601String();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function room()
    {
        return $this->hasOne('App\Models\Room');
    }

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }

    public function reservations(){
        return $this->hasMany('App\Models\ViewingReservation');
    }
}