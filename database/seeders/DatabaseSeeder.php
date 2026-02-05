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
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'user_role_id' => UserRoles::where('name','user')->first()->id,
        ]);
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'testadmin@example.com',
            'user_role_id' => UserRoles::where('name','admin')->first()->id,
        ]);
        User::factory()->create([
            'name' => 'Test User 2',
            'email' => 'testuser2@example.com',
            'user_role_id' => UserRoles::where('name','user')->first()->id,
        ]);
        $this->call(BuildingSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(HubSeeder::class);
        $this->call(FoundSeeder::class);
        $this->call(FoundImagesSeeder::class);
    }
}
