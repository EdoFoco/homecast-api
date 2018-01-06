<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DescriptionSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'property_id',
        'title', 
        'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'property_id'
    ];

    public function property(){
        return $this->belongsTo('App\Models\Property');
    }
}