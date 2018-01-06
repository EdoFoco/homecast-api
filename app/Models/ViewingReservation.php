<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ViewingReservation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'viewing_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'user_id', 'viewing_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function viewing()
    {
        return $this->belongsTo('App\Models\Viewing');
    }
}