<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Building;
use App\Models\Room;

class Hub extends Model
{
    use HasFactory;
    protected $fillable = [
        'hub_name',
        'hub_description',
        'room_id'
    ];
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
