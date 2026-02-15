<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Found;
use App\Models\FoundCategory;
use App\Models\FoundStatus;

class FoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
           'Elektronik',
            'Dokumen dan identitas',
            'Tas dan Dompet',
            'Nominal',
            'Kendaraan dan Perlengkapannya',
            'Pakaian dan Aksesoris',
            'Lainnya'
        ];
        $statuses = [
            'Ditemukan',
            'Hilang',
            'Dikembalikan',
            'Tersimpan'
        ];
        $founds = [
            [1,1,1,1,1,"Barang berwarna hitam dengan corak stiker","Dompet hitam",null,'2026-02-04 15:30:00'],
            [1,2,null,2,2,"Barang berwarna putih dengan ujung lancip","Pisau Meteor",null,'2026-02-06 15:30:00'],
            [3,4,2,3,3,"Barang berwarna biru dengan bentuk diamond","Kertas Magic hitam",null,'2026-02-03 15:30:00'],
            [3,4,null,3,2,"Barang berwarna biru dengan bentuk diamond","Kertas Magic hitam",null,'2026-01-03 15:30:00'],
            [3,4,1,3,3,"Barang berwarna biru dengan bentuk diamond","Kertas Magic hitam",null,'2026-04-03 15:30:00'],
            [3,4,null,3,1,"Barang berwarna biru dengan bentuk diamond","Kertas Magic hitam",null,'2026-04-03 15:30:00']
        ];

       
        foreach ($categories as $category) {
            FoundCategory::firstOrCreate(
                ['name'=> $category]
            );
        }
        foreach ($statuses as $status) {
            FoundStatus::firstOrCreate(
                ['name'=> $status]
            );
        }
        
        foreach($founds as [$user_id, $room_id, $location_hub_id, $found_category_id,
         $found_status_id, $found_description, $found_name,$found_phone_number, $found_date]){
              Found::create(
                    [
                        'found_name' => $found_name,
                        'user_id' => $user_id,
                        'room_id'=> $room_id,
                        'location_hub_id'=> $location_hub_id,
                        'found_category_id'=> $found_category_id,
                        'found_status_id'=> $found_status_id,
                        'found_description'=> $found_description,
                        'found_phone_number'=> $found_phone_number,
                        'found_date'=> $found_date
                ]
            );
          }
    }
}
