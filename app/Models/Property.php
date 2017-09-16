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
        'name', 'address', 'postcode', 'city', 'user_id', 'thumbnail', 'description', 'price'
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
}