<?php

namespace App\Models;
use App\Models\User;
use App\Models\Room;
use App\Models\Hub;
use App\Models\FoundCategory;
use App\Models\FoundStatus;
use App\Models\FoundImages;
use Illuminate\Database\Eloquent\Model;

class Found extends Model
{
    
    protected $fillable = [
        'user_id',
        'room_id',
        'location_hub_id',
        'found_category_id',
        'found_status_id',
        'found_description',
        'found_name',
        'found_phone_number',
        'found_date'
    ];
    protected static function booted(){
        static::deleting(function ($found) {
                foreach($found->foundImages as $image){
                    if($image->image_path){
                        Storage::disk('public')->delete($image->image_path);
                    }
                }
            });
    }

    public function foundImages() {
        return $this->hasMany(FoundImages::class);
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
    
}
