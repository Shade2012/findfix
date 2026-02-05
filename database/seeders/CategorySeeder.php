<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'icon' => 'electronics'],
            ['name' => 'Dokumen dan Identitas', 'icon' => 'document'],
            ['name' => 'Tas dan Dompet', 'icon' => 'bag'],
            ['name' => 'Kendaraan dan Perlengkapannya', 'icon' => 'vehicle'],
            ['name' => 'Pakaian dan Aksesoris', 'icon' => 'clothing'],
            ['name' => 'Lainnya', 'icon' => 'other'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
