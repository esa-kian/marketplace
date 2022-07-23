<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitre extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'mitre_num', 'type'];

    public function rules()
    {
        return $this->belongsToMany('App\Models\Rule');
    }

    public function applications()
    {
        return $this->belongsToMany('App\Models\Application');
    }
}
