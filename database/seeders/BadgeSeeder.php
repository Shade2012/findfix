<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $badges = [
            ['Eagle Eye','Orang pertama yang melaporkan di lokasi tertentu','🦅',1],
            ['Honesty Hero','Menemukan lebih dari 3 barang ','👁️',3],
        ];

        foreach($badges as [$found_id,$image_path]){
            Badge::create([
                'found_id'   => $found_id,
                'image_path' => $image_path,
            ]);
        }
    }
}
