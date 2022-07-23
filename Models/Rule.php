<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rule extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function content()
    {
        return $this->hasOne('App\Models\Content');
    }

    public function useCases()
    {
        return $this->belongsToMany('App\Models\UseCase');
    }

    public function mitres()
    {
        return $this->belongsToMany('App\Models\Mitre');
    }

    public function logData()
    {
        return $this->belongsToMany('App\Models\LogData');
    }

    public function logSources()
    {
        return $this->belongsToMany('App\Models\LogSource');
    }

    public function osPlatforms()
    {
        return $this->belongsToMany('App\Models\OsPlatform');
    }
}
