<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Room extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'unique_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function viewing()
    {
        return $this->belongsTo('App\Models\Viewing');
    }

    public function messages()
    {
        return $this->hasMany('App\Models\Message');
    }
}