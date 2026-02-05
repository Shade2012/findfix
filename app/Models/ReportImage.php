<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReportImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'lost_found_item_id',
        'image_path',
        'order'
    ];

    protected $appends = ['image_url'];

    // Relationship
    public function lostFoundItem()
    {
        return $this->belongsTo(LostFoundItem::class);
    }

    // Accessor to get full image URL
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
