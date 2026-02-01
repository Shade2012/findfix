<?php

namespace Database\Seeders;

use App\Models\UserRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      foreach (['admin', 'user'] as $role) {
            UserRoles::firstOrCreate(['name' => $role]);
        }
    }
}
