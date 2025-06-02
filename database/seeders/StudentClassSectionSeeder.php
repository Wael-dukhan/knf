<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassSection;
use App\Models\StudentClassSection;
use Carbon\Carbon;

class StudentClassSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب جميع الشُعب الدراسية
        $classSections = ClassSection::with('grade')->get();

        // الوقت الحالي
        $currentTime = Carbon::now();

        foreach ($classSections as $classSection) {
            $academicYearId = $classSection->grade->academic_year_id;

            if (!$academicYearId) {
                $this->command->warn("الشعبة {$classSection->id} لا تحتوي على سنة دراسية.");
                continue;
            }

            // جلب عدد عشوائي من الطلاب (مثلاً 5 لكل شعبة)
            $students = User::role('student')
                ->where('school_id', $classSection->grade->school_id)
                ->whereDoesntHave('classSections', function ($query) use ($academicYearId) {
                    $query->where('student_class_section.academic_year_id', $academicYearId);
                })
                ->inRandomOrder()
                ->take(5)
                ->get();

            foreach ($students as $student) {
                // ربط الطالب بالشعبة
                $student->classSections()->attach($classSection->id, [
                    'academic_year_id' => $academicYearId,
                    'status' => 'active',
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ]);

                $this->command->info("تم تعيين الطالب {$student->id} للشعبة {$classSection->id} للسنة الدراسية {$academicYearId}");
            }
        }
    }
}
