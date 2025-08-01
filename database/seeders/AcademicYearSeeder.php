<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();

        if (!$school) {
            $this->command->error('⚠️ لا توجد مدارس في قاعدة البيانات. الرجاء إنشاء مدرسة أولاً.');
            return;
        }

        $academicYearsData = [
            [
                'name' => '2023/2024',
                'start_date' => '2023-09-01',
                'end_date' => '2024-06-30',
                'status' => 'inactive',
            ],
            [
                'name' => '2024/2025',
                'start_date' => '2024-09-01',
                'end_date' => '2025-06-30',
                'status' => 'active',
            ],
        ];

        foreach ($academicYearsData as $yearData) {
            $academicYear = AcademicYear::create([
                'name' => $yearData['name'],
                'school_id' => $school->id,
                'start_date' => $yearData['start_date'],
                'end_date' => $yearData['end_date'],
                'status' => $yearData['status'],
            ]);

            $this->createGradesForAcademicYear($academicYear, $school->id);
        }
    }

    private function createGradesForAcademicYear(AcademicYear $academicYear, int $schoolId)
    {
        $grades = [
            1 => 'الصف الأول',
            2 => 'الصف الثاني',
            3 => 'الصف الثالث',
            4 => 'الصف الرابع',
            5 => 'الصف الخامس',
            6 => 'الصف السادس',
            7 => 'الصف السابع',
            8 => 'الصف الثامن',
            9 => 'الصف التاسع',
            10 => 'الصف العاشر',
            11 => 'الصف الحادي عشر',
            12 => 'البكالوريا',
        ];

        foreach ($grades as $number => $name) {
            $gradeLevel = $this->determineGradeLevel($number);

            if ($gradeLevel === 3) {
                foreach (['science' => 'علمي', 'literary' => 'أدبي'] as $trackKey => $trackLabel) {
                    Grade::create([
                        'name' => $name . ' - ' . $trackLabel,
                        'grade_number' => $number,
                        'grade_level' => $gradeLevel,
                        'track' => $trackKey,
                        'school_id' => $schoolId,
                        'academic_year_id' => $academicYear->id,
                        'description' => 'وصف ' . $name . ' - ' . $trackLabel,
                    ]);
                }
            } else {
                Grade::create([
                    'name' => $name,
                    'grade_number' => $number,
                    'grade_level' => $gradeLevel,
                    'track' => null,
                    'school_id' => $schoolId,
                    'academic_year_id' => $academicYear->id,
                    'description' => 'وصف ' . $name,
                ]);
            }
        }
    }

    private function determineGradeLevel(int $gradeNumber): int
    {
        if ($gradeNumber >= 1 && $gradeNumber <= 4) {
            return 1; // ابتدائي
        } elseif ($gradeNumber >= 5 && $gradeNumber <= 9) {
            return 2; // إعدادي
        }
        return 3; // ثانوي
    }
}
