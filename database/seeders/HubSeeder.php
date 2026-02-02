<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hub;
class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hubs = [
            ['Hub 1 di Miracle','Hub di Miracle Ruangan Audi',3],
            ['Hub 2 di Smart','Hub di Smart ruangan Audi',5],

        ];
        foreach($hubs as [$hub_name,$hub_description,$room_id]){
              Hub::firstOrCreate(
                ['hub_name' => $hub_name],
                    [
                    'hub_description' => $hub_description,
                    'room_id' => $room_id,
                ]
            );
          }
    }
}
