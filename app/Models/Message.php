<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at'
    ];

    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}