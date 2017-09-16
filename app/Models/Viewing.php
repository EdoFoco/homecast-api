<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Viewing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_time', 'user_id', 'property_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function room()
    {
        return $this->hasOne('App\Models\Room');
    }

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }
}