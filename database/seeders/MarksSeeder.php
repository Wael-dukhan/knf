<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mark;
use App\Models\User;  // الطلاب
use App\Models\Material;  // المواد
use App\Models\Term;  // الفصول الدراسية
use App\Models\MaterialTeacherAssignment; // تعيينات المعلم للمادة في الشعبة
use App\Models\AcademicYear;

class MarksSeeder extends Seeder
{
    public function run()
    {
        $students = User::role('student')->get();
        $materials = Material::all();
        $terms = Term::all();

        if ($students->isEmpty() || $materials->isEmpty() || $terms->isEmpty()) {
            $this->command->info('لا يوجد طلاب، مواد أو فصول دراسية لإنشاء علامات.');
            return;
        }

        // الحصول على السنة الدراسية الحالية (يمكن تعديلها حسب المنطق الخاص بك)
        $today = now()->toDateString();
        $currentAcademicYear = AcademicYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$currentAcademicYear) {
            $this->command->info('لا توجد سنة دراسية حالية.');
            return;
        }

        foreach ($students as $student) {
            // استرجاع الشعبة الخاصة بالطالب للسنة الدراسية الحالية
            $classSection = $student->classSections()
                ->wherePivot('academic_year_id', $currentAcademicYear->id)
                ->wherePivot('status', 'active')
                ->first();

            if (!$classSection) {
                // إذا الطالب ليس له شعبة نشطة للسنة الدراسية الحالية تجاهل
                continue;
            }

            $classSectionId = $classSection->id;

            foreach ($materials as $material) {
                // تحقق وجود معلم معين للمادة في الشعبة
                $assignmentExists = MaterialTeacherAssignment::where('material_id', $material->id)
                    ->where('class_section_id', $classSectionId)
                    ->exists();

                if (!$assignmentExists) {
                    // لا يوجد معلم معين، تخطى إدخال علامات لهذه المادة
                    continue;
                }

                foreach ($terms as $term) {
                    $oralMark = rand(50, 100);
                    $homeworkMark = rand(50, 100);
                    $firstStudyMark = rand(50, 100);
                    $secondStudyMark = rand(50, 100);
                    $workTotal = ceil(($oralMark + $homeworkMark + $firstStudyMark + $secondStudyMark) / 4);
                    $oralExamMark = rand(50, 100);
                    $writtenExamMark = rand(50, 100);
                    $termTotal = ceil(($workTotal + ceil(($oralExamMark + $writtenExamMark) / 2)) / 3);

                    Mark::create([
                        'student_id' => $student->id,
                        'material_id' => $material->id,
                        'term_id' => $term->id,
                        'class_section_id' => $classSectionId,  // <== أضف هذا السطر
                        'academic_year_id' => $term->academic_year_id,  // <== أضف هذا السطر
                        'oral_mark' => $oralMark,
                        'homework_mark' => $homeworkMark,
                        'first_study_mark' => $firstStudyMark,
                        'second_study_mark' => $secondStudyMark,
                        'work_total' => $workTotal,
                        'oral_exam_mark' => $oralExamMark,
                        'written_exam_mark' => $writtenExamMark,
                        'term_total' => $termTotal,
                    ]);
                }
            }
        }

        $this->command->info('تم إنشاء علامات عشوائية للطلاب بنجاح.');
    }
}
