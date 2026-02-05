<?php

namespace App\Models;

use App\Models\Found;
use Illuminate\Database\Eloquent\Model;

class FoundCategory extends Model
{
     protected $fillable=[
        "name"
    ];

    public function founds(){
        return $this->hasMany(Found::class);
    }
}
