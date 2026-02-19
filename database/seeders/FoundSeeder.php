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

        // miracle 2
        // smart 4
        
        $founds = [
            

            // User 1 Hilang
            [1,4,null,3,2,"Barang berwarna hitam dengan corak stiker","Dompet hitam", '082124805253','2026-02-04 15:30:00'], //Done
            [1,2,null,7,2,"Barang berwarna putih dengan ujung lancip","Pisau Meteor",'082124805253','2026-02-06 15:30:00'], //Done

            // User 1 Ditemukan
            [1,4,2,3,1,"Hp berwarna hitam merek xiaomi","Hp Xiaomi Hitam",null,'2026-02-03 15:30:00'], //Done
            [1,2,1,7,1,"Barang minuman warna hitam dengan gagang abu-abu","Tumbler Hitam",null,'2026-01-03 15:30:00'], //Done

            // User 1 Dikembalikan
            [1,4,2,1,3,"Flaskdisk berwarna hitam merek sandisk dengan font berwarna merah","Flaskdisk hitam",null,'2026-04-03 15:30:00'], //Done // nyambung dengan user 2 tersimpan

            // User 2 Hilang
            [2,4,null,3,2,"Hp hitam merek xiaomi","Hp Xiaomi Hitam",'082726567283','2026-02-03 15:30:00'], //Done
            [2,1,null,5,2,"Surat tanda nomor kendaraan atas name Ryan","STNK",'082726567283','2026-01-03 15:30:00'], //Done

            // $user_id, $room_id, $location_hub_id, $found_category_id, $found_status_id, $found_description, $found_name,$found_phone_number, $found_date 

            // User 2 Temu
            [2,4,2,3,1,"Barang berwarna hitam dengan corak stiker kecil","Dompet stiker hitam",null,'2026-01-03 15:30:00'], //Done
            [2,2,1,5,1,"Kunci motor gigi 5","Kunci Motor",null,'2026-04-03 15:30:00'], //Done
            [2,2,1,7,1,"Pulpen hitam dengan penutup putih","Pulpen",null,'2026-04-03 15:30:00'], //Done

            // User 2 Tersimpan
            [2,4,2,1,4,"Flaskdisk warna hitam merek sandisk","Flaskdisk sandisk",null,'2026-04-03 15:30:00'], //Done // nyambung dengan user 1 dikembalika 
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
