<?php

namespace App\Models;
use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'no_room',
        'building_id',
        'description'
    ];

    public function hub() {
        return $this->hasOne(Hub::class);
    }
    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }
}
