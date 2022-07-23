<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UseCase extends Model
{
    use HasFactory;

    protected $fillable = ['title'];
    
    public function rules()
    {
        return $this->belongsToMany('App\Models\Rule');
    }
}
