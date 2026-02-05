<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Model;
use App\Models\Found;

class FoundImages extends Model
{
    protected $appends = ['found_img_url'];
    protected $fillable = [
        'found_id',
        'image_path'
    ];

    public function found(){
        return $this->belongsTo(Found::class);
    }

    public function getFoundImgUrlAttribute()
    {
        if ($this->image_path) {
           return url('/storage/' . $this->image_path);
        }
    return url('/images/placeholder.png');
    }
}

