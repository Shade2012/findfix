<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
    public function run(): void
    {
        $buildings = [
            ['Dago', 'Gedung kampus dago'],
            ['Miracle Building', 'Gedung miracle atau gedung lama'],
            ['Smart Building', 'Gedung smart atau gedung hotel'],
        ];

        foreach($buildings as [$name, $desc]){
                Building::firstOrCreate(
                ['building_name' => $name],
                ['description' => $desc]
            );
        }
    }
}
