<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PropertyImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'property_id', 'url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function property()
    {
        return $this->belongsTo('App\Models\Property');
    }
}