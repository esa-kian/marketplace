<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['comment',];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function content()
    {
        return $this->belongsTo('App\Models\Content');
    }

    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }

    public function replies()
    {
        return $this->hasMany('App\Models\Comment', 'parent_id');
    }
}
