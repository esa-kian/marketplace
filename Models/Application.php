<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['overview'];

    public function content()
    {
        return $this->hasOne('App\Models\Content');
    }

    public function mitres()
    {
        return $this->belongsToMany('App\Models\Mitre');
    }

    public function pictures()
    {
        return $this->hasMany('App\Models\Picture');
    }

    public function attachments()
    {
        return $this->hasMany('App\Models\Attachment');
    }
}
