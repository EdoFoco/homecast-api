<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewingStatus extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status'
    ];

    public function viewings()
    {
        return $this->hasMany('App\Models\Viewing');
    }
}