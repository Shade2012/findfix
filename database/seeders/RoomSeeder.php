<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $rooms = [
            ['Dago 5001',1,'Ruangan di Dago'],
            ['6001',2,'Ruangan di Miracle'],
            ['5600',3,'Ruangan di Smart']
        ];

          foreach($rooms as [$no_room,$building_id,$description]){
              Room::firstOrCreate(
                ['no_room' => $no_room],
                [
                    'building_id' => $building_id,
                    'description' => $description,
                ]
            );
          }
    }
}
