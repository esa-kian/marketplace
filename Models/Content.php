<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    public function rule()
    {
        return $this->belongsTo('App\Models\Rule');
    }

    public function application()
    {
        return $this->belongsTo('App\Models\Application');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
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

    public function votes()
    {
        return $this->hasMany('App\Models\Vote');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag');
    }
}
