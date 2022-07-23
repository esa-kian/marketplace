<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogData extends Model
{
    use HasFactory;

    public function rules()
    {
        return $this->belongsToMany('App\Models\Rule');
    }
}
