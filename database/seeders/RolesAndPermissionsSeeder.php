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
        $roles = ['super_admin', 'school_manager', 'teacher', 'quran_teacher', 'quran_supervisor', 'student', 'parent', 'educational_supervisor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ربط الصلاحيات بالأدوار
        $rolesWithPermissions = [
            'super_admin' => [
                'manage users', 'assign roles', 'view user activity', 'manage schools',
                'supervise schools', 'manage teachers', 'view teachers', 'evaluate teachers',
                'add students', 'edit students', 'delete students', 'view students',
                'assign students to class', 'track student progress', 'view parents',
                'communicate with parents', 'access dashboard', 'view reports', 'generate reports',
                'manage exams', 'grade exams', 'view grades', 'send notifications',
                'view messages', 'send messages',
                // صلاحيات قرآنية
                'manage quran classes', 'assign quran teachers', 'view quran progress',
                'evaluate quran teachers', 'view quran reports', 'send quran notifications',
                'track quran progress', 'assign quran memorization', 'mark quran attendance',
                'view quran students'
            ],

            'school_manager' => [
                'manage teachers', 'view teachers', 'add students', 'edit students',
                'view students', 'assign students to class', 'track student progress',
                'view parents', 'communicate with parents', 'access dashboard', 'view reports',
                'generate reports', 'manage exams', 'view grades', 'send notifications',
                'view messages',
                // إشراف قرآني داخل المدرسة
                'manage quran classes', 'assign quran teachers', 'view quran progress',
                'evaluate quran teachers', 'view quran reports', 'send quran notifications'
            ],

            'educational_supervisor' => [
                'view teachers', 'evaluate teachers', 'view students', 'track student progress',
                'supervise schools', 'access dashboard', 'view reports', 'send notifications',
            ],

            'teacher' => [ // معلم المدرسة الأكاديمي
                'view students', 'track student progress', 'grade exams',
                'view grades', 'view messages', 'send messages'
            ],

            'quran_teacher' => [
                'view quran students', 'track quran progress', 'assign quran memorization',
                'mark quran attendance', 'send quran notifications',
                'view messages', 'send messages'
            ],

            'quran_supervisor' => [
                'manage quran classes', 'assign quran teachers', 'view quran progress',
                'evaluate quran teachers', 'view quran reports', 'send quran notifications'
            ],

            'student' => [
                'view grades', 'view messages', 'send messages',
            ],

            'parent' => [
                'view students', 'view grades', 'communicate with parents', 'view messages',
            ],
        ];

        // إنشاء الصلاحيات
        $allPermissions = collect($rolesWithPermissions)->flatten()->unique();
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ربط الصلاحيات بالأدوار
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

            // معلمي المدرسة
            for ($j = 1; $j <= 3; $j++) {
                $teacherUser = User::create([
                    'name' => 'Teacher ' . $j . ' - School ' . $i,
                    'email' => 'teacher' . $i . $j . '@example.com',
                    'password' => bcrypt('password123'),
                    'school_id' => $school->id,
                ]);
                $teacherUser->assignRole('teacher');
            }

            // معلمي الحلقات القرآنية
            for ($j = 1; $j <= 2; $j++) {
                $quranTeacher = User::create([
                    'name' => 'Quran Teacher ' . $j . ' - School ' . $i,
                    'email' => 'qteacher' . $i . $j . '@example.com',
                    'password' => bcrypt('password123'),
                    'school_id' => $school->id,
                ]);
                $quranTeacher->assignRole('quran_teacher');
            }

            // مشرف الحلقات القرآنية
            $quranSupervisor = User::create([
                'name' => 'Quran Supervisor - School ' . $i,
                'email' => 'qsupervisor' . $i . '@example.com',
                'password' => bcrypt('password123'),
                'school_id' => $school->id,
            ]);
            $quranSupervisor->assignRole('quran_supervisor');

            // الطلاب والأهالي
            for ($k = 1; $k <= 10; $k++) {
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

                $studentUser->parents()->attach($parentUser->id);
            }
        }
    }
}
