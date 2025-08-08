<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();

        if (!$school) {
            $this->command->error('⚠️ لا توجد مدارس في قاعدة البيانات. الرجاء إنشاء مدرسة أولاً.');
            return;
        }

        $now = Carbon::now();

        // السنة الدراسية تبدأ من سبتمبر حتى يونيو
        // تحديد السنة الدراسية الحالية: لو الآن قبل سبتمبر، السنة الدراسية هي السنة السابقة - السنة الحالية
        // وإلا السنة الحالية - السنة القادمة

        // حساب بداية السنة الدراسية الحالية (1 سبتمبر)
        $currentYearStart = Carbon::create($now->year, 9, 1);

        if ($now->lt($currentYearStart)) {
            // إذا التاريخ قبل 1 سبتمبر، السنة الدراسية الحالية هي السنة السابقة (مثلاً الآن أغسطس 2025، السنة 2024/2025)
            $startYear = $now->year - 1;
            $endYear = $now->year;
        } else {
            // إذا التاريخ بعد 1 سبتمبر أو يساويه، السنة الدراسية الحالية هي السنة الحالية - السنة القادمة (مثلاً أكتوبر 2025 => 2025/2026)
            $startYear = $now->year;
            $endYear = $now->year + 1;
        }

        // البيانات للسنة الحالية والسنة السابقة
        $academicYearsData = [
            [
                'name' => ($startYear - 1) . '/' . $startYear,
                'start_date' => Carbon::create($startYear - 1, 9, 1)->toDateString(),
                'end_date' => Carbon::create($startYear, 8, 30)->toDateString(),
                'status' => 'inactive',
            ],
            [
                'name' => $startYear . '/' . $endYear,
                'start_date' => Carbon::create($startYear, 9, 1)->toDateString(),
                'end_date' => Carbon::create($endYear, 8, 30)->toDateString(),
                'status' => 'active',
            ],
        ];

        foreach ($academicYearsData as $yearData) {
            $academicYear = AcademicYear::firstOrCreate([
                'name' => $yearData['name'],
                'school_id' => $school->id,
            ], [
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
                    Grade::firstOrCreate([
                        'name' => $name . ' - ' . $trackLabel,
                        'grade_number' => $number,
                        'grade_level' => $gradeLevel,
                        'track' => $trackKey,
                        'school_id' => $schoolId,
                        'academic_year_id' => $academicYear->id,
                    ], [
                        'description' => 'وصف ' . $name . ' - ' . $trackLabel,
                    ]);
                }
            } else {
                Grade::firstOrCreate([
                    'name' => $name,
                    'grade_number' => $number,
                    'grade_level' => $gradeLevel,
                    'track' => null,
                    'school_id' => $schoolId,
                    'academic_year_id' => $academicYear->id,
                ], [
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
