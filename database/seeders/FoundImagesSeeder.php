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
            [2,'images/knife_meteorite_test561709.jpg']
        ];

        foreach($foundImages as [$found_id,$image_path]){
            FoundImages::create([
                'found_id'   => $found_id,
                'image_path' => $image_path,
            ]);
        }
    }
}
