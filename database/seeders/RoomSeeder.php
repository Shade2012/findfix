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
            ['5001',1,'Ruangan di Dago'],
            ['6001',2,'Ruangan di Miracle'],
            ['7819',2,'Audi di miracle'],
            ['5600',3,'Ruangan di Smart'],
            ['3700',3,'Audi di Smart']
        ];

          foreach($rooms as [$name_room,$building_id,$description]){
              Room::firstOrCreate(
                ['name_room' => $name_room],
                [
                    'building_id' => $building_id,
                    'description' => $description,
                ]
            );
          }
    }
}
