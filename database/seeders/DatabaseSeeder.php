<?php

namespace Database\Seeders;

use App\Models\UserRoles;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(UserRoleSeeder::class);
        User::factory()->create([
            'name' => 'Damar Fikri Haikal',
            'email' => 'damar.10125289@mahasiswa.unikom.ac.id',
            'user_role_id' => UserRoles::where('name','user')->first()->id,
        ]);
        User::factory()->create([
            'name' => 'Ryan',
            'email' => 'ryan.10124310@mahasiswa.unikom.co.id',
            'user_role_id' => UserRoles::where('name','user')->first()->id,
        ]);
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'testadmin@example.com',
            'user_role_id' => UserRoles::where('name','admin')->first()->id,
        ]);
        $this->call(BuildingSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(HubSeeder::class);
        $this->call(FoundSeeder::class);
        $this->call(FoundImagesSeeder::class);
        $this->call(BadgeSeeder::class);
    }
}
