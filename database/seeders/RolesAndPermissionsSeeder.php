<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\School;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        $roles = ['super_admin', 'school_manager', 'teacher', 'student', 'parent', 'educational_supervisor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ربط الصلاحيات بالأدوار
        $rolesWithPermissions = [
            'super_admin' => [
                'manage users',
                'assign roles',
                'view user activity',
                'manage schools',
                'supervise schools',
                'manage teachers',
                'view teachers',
                'evaluate teachers',
                'add students',
                'edit students',
                'delete students',
                'view students',
                'assign students to class',
                'track student progress',
                'view parents',
                'communicate with parents',
                'access dashboard',
                'view reports',
                'generate reports',
                'manage exams',
                'grade exams',
                'view grades',
                'send notifications',
                'view messages',
                'send messages',
            ],
            
            'school_manager' => [
                'manage teachers',
                'view teachers',
                'add students',
                'edit students',
                'view students',
                'assign students to class',
                'track student progress',
                'view parents',
                'communicate with parents',
                'access dashboard',
                'view reports',
                'generate reports',
                'manage exams',
                'view grades',
                'send notifications',
                'view messages',
            ],
        
            'educational_supervisor' => [
                'view teachers',
                'evaluate teachers',
                'view students',
                'track student progress',
                'supervise schools',
                'access dashboard',
                'view reports',
                'send notifications',
            ],
        
            'teacher' => [
                'view students',
                'track student progress',
                'grade exams',
                'view grades',
                'view messages',
                'send messages',
            ],
        
            'student' => [
                'view grades',
                'view messages',
                'send messages',
            ],
        
            'parent' => [
                'view students',
                'view grades',
                'communicate with parents',
                'view messages',
            ],
        ];

        // إنشاء جميع الصلاحيات تلقائيًا من الأدوار
        $allPermissions = collect($rolesWithPermissions)->flatten()->unique();

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ($rolesWithPermissions as $role => $perms) {
            $roleInstance = Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $roleInstance->syncPermissions($perms);
        }

        // إنشاء المستخدم الرئيسي
        $adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);
        $adminUser->assignRole('super_admin');

        // إنشاء مستخدم مشرف تربوي
        $supervisorUser = User::create([
            'name' => 'Educational Supervisor',
            'email' => 'supervisor@example.com',
            'password' => bcrypt('password123'),
        ]);
        $supervisorUser->assignRole('educational_supervisor');

        // إنشاء مدارس ومدراء ومدرسين وطلاب وأهالي
        for ($i = 1; $i <= 3; $i++) {
            $school = School::create([
                'name' => 'School ' . $i,
            ]);

            // مدير المدرسة
            $schoolManagerUser = User::create([
                'name' => 'School Manager ' . $i,
                'email' => 'school_manager' . $i . '@example.com',
                'password' => bcrypt('password123'),
                'school_id' => $school->id,
            ]);
            $schoolManagerUser->assignRole('school_manager');

            // المدرسين
            for ($j = 1; $j <= 5; $j++) {
                $teacherUser = User::create([
                    'name' => 'Teacher ' . $j . ' - School ' . $i,
                    'email' => 'teacher' . $i . $j . '@example.com',
                    'password' => bcrypt('password123'),
                    'school_id' => $school->id,
                ]);
                $teacherUser->assignRole('teacher');
            }

            // الطلاب والأهالي
            for ($k = 1; $k <= 50; $k++) {
                $studentUser = User::create([
                    'name' => 'Student ' . $k . ' - School ' . $i,
                    'email' => 'student' . $i . $k . '@example.com',
                    'password' => bcrypt('password123'),
                    'school_id' => $school->id,
                ]);
                $studentUser->assignRole('student');

                $parentUser = User::create([
                    'name' => 'Parent ' . $k . ' - School ' . $i,
                    'email' => 'parent' . $i . $k . '@example.com',
                    'password' => bcrypt('password123'),
                    'school_id' => $school->id,
                ]);
                $parentUser->assignRole('parent');

                // ربط الطالب بولي الأمر
                $studentUser->parents()->attach($parentUser->id);
            }
        }
    }
}
