<?php

namespace App\Models;
use App\Models\User;
use App\Models\Room;
use App\Models\Hub;
use App\Models\FoundCategory;
use App\Models\FoundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Found extends Model
{
    protected $appends = ['found_img_url'];
    protected $fillable = [
        'user_id',
        'room_id',
        'location_hub_id',
        'found_category_id',
        'found_status_id',
        'found_description',
        'found_name',
        'found_phone_number',
        'found_img',
        'found_date'
    ];
    protected static function booted(){
        static::deleting(function ($found) {
            if ($found->found_img) {
                Storage::disk('public')->delete($found->found_img);
            }
        });
    }

    public function status(){
        return $this->belongsTo(FoundStatus::class,'found_status_id');
    }
    public function category(){
        return $this->belongsTo(FoundCategory::class,'found_category_id');
    }
    public function hub(){
        return $this->belongsTo(Hub::class,'location_hub_id');
    }
    public function room(){
        return $this->belongsTo(Room::class,'room_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function getFoundImgUrlAttribute()
    {
        return $this->found_img ? Storage::disk('public')->url($this->found_img): url(path: '/images/placeholder.png');
    }
    

}
