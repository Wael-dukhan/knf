<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $this->call(RoleSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);
        
        
        $this->call(SchoolSeeder::class);
        
        $this->call(AcademicYearSeeder::class);

        $this->call([TermsSeeder::class]);


        // الصفوف الدراسية
        $this->call(GradeSeeder::class);

        // الشعب الدراسية
        $this->call(ClassSectionSeeder::class);

        // الأنشطة
        $this->call(MaterialSeeder::class);
        
        $this->call(StudentClassSectionSeeder::class);

        $this->call(AssignTeacherToMaterialInClassSectionSeeder::class);
    }
}
