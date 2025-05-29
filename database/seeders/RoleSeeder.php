<?php

// database/seeders/RoleSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // إضافة الأدوار
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'school_manager']);
        Role::create(['name' => 'educational_supervisor']);
        Role::create(['name' => 'teacher']);
        Role::create(['name' => 'parent']);
        Role::create(['name' => 'student']);
        // Role::create(['name' => 'student']);
    }
}
