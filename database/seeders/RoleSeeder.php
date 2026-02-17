<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manajemen']);
        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'designer']);
        Role::create(['name' => 'operator indoor']);
        Role::create(['name' => 'operator outdoor']);
        Role::create(['name' => 'operator multi']);
        Role::create(['name' => 'operator dtf']);
        Role::create(['name' => 'inventory utama']);
        Role::create(['name' => 'inventory cabang']);
        Role::create(['name' => 'advertising']);
    }
}
