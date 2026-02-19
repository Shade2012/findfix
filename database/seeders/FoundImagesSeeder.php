<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoundImages;

class FoundImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foundImages = [
            [1,'images/black_wallet_1_test561709.jpg'],
            [1,'images/black_wallet_2_test561709.jpg'],
            [2,'images/knife_meteorite_test561709.jpg'],

            [3,'images/hp.jpg'],
            [4,'images/tumbler_hitam.jpg'],
            [5,'images/flaskdisk.jpg'],

            [6,'images/hp_found.jpg'],
            [7,'images/stnk.jpg'],
            [8,'images/black_wallet_2_test561709_found.jpg'],
            [9,'images/kunci_motor.jpg'],
            [10,'images/pulpen.jpg'],
            [11,'images/flaskdisk_found.jpg']
        ];

        foreach($foundImages as [$found_id,$image_path]){
            FoundImages::create([
                'found_id'   => $found_id,
                'image_path' => $image_path,
            ]);
        }
    }
}
