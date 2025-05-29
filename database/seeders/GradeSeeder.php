<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::first();
        $school = School::first();

        // تحقق من وجود القيم الأساسية
        if (!$academicYear || !$school) {
            $this->command->error('تأكد من وجود العام الدراسي والمدرسة والمراحل الدراسية.');
            return;
        }

        $grades = [
            ['name' => 'الصف الأول', 'description' => 'الوصف الأول'],
            ['name' => 'الصف الثاني', 'description' => 'الوصف الثاني'],
            ['name' => 'الصف الثالث', 'description' => 'الوصف الثالث'],
        ];

        foreach ($grades as $grade) {
            Grade::create([
                'name' => $grade['name'],
                'description' => $grade['description'],
                'academic_year_id' => $academicYear->id,
                'school_id' => $school->id,
                'grade_level' => 1, // ربط الصف بالمرحلة
            ]);
        }

        $this->command->info('تم إدخال الصفوف الدراسية بنجاح.');
    }
}
