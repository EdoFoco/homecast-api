<?php

namespace App;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'profile_picture', 'about', 'device_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'updated_at', 'device_token'
    ];

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function properties()
    {
        return $this->hasMany('App\Models\Property');
    }

    public function viewings()
    {
        return $this->hasMany('App\Models\Viewing');
    }

    public function viewingReservations()
    {
        return $this->hasMany('App\Models\ViewingReservation');
    }

    public function viewingInvitations(){
        return $this->hasMany('App\Models\ViewingInvitation');
    }
    
}
