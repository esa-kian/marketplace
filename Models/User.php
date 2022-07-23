<?php

namespace App\Models;

use App\Permissions\HasPermissionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasPermissionsTrait; //Import The Trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MailResetPasswordNotification($token));
    }

    public function contents()
    {
        return $this->hasMany('App\Models\Content');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }

    public function votes()
    {
        return $this->hasMany('App\Models\Vote');
    }

    public function views()
    {
        return $this->hasMany('App\Models\View');
    }

    public function downloads()
    {
        return $this->hasMany('App\Models\Download');
    }
    
    public function accessible()
    {
        return $this->hasMany('App\Models\Accessible');
    }

    public function person()
    {
        return $this->hasOne('App\Models\Person');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Company');
    }

    public function subscriptionPlan()
    {
        return $this->hasOne('App\Models\SubscriptionPlan');
    }

    public function requests()
    {
        return $this->hasMany('App\Models\Request');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }
}
