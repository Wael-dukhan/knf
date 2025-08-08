<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\User;
use App\Models\ClassSection;
use App\Models\MaterialTeacherAssignment;
use App\Models\School;
use Illuminate\Database\Seeder;

class AssignTeacherToMaterialInClassSectionSeeder extends Seeder
{
    public function run()
    {
        $academicYearId = 1; // تأكد من وجود السنة الدراسية
        $termId = 1;         // تأكد من وجود الفصل الدراسي

        $schools = School::all();

        foreach ($schools as $school) {
            // جلب معلم واحد لكل مدرسة (يمكنك تعديل لجلب معلمين متعددين)
            $teacher = User::role('teacher')->where('school_id', $school->id)->first();
            if (!$teacher) {
                $this->command->info("لم يتم العثور على معلم في المدرسة ID: {$school->id}");
                continue;
            }

            // جلب جميع الشعب المرتبطة بهذه المدرسة (عن طريق المرحلة)
            $classSections = ClassSection::whereHas('grade', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })->get();

            if ($classSections->isEmpty()) {
                $this->command->info("لم يتم العثور على شعب دراسية في المدرسة ID: {$school->id}");
                continue;
            }

            foreach ($classSections as $classSection) {
                // جلب جميع المواد المرتبطة بنفس المرحلة (grade_id)
                $materials = Material::where('grade_id', $classSection->grade_id)->get();

                if ($materials->isEmpty()) {
                    $this->command->info("لم يتم العثور على مواد للمرحلة ID: {$classSection->grade_id}");
                    continue;
                }

                foreach ($materials as $material) {
                    MaterialTeacherAssignment::updateOrCreate(
                        [
                            'academic_year_id' => $academicYearId,
                            'term_id' => $termId,
                            'material_id' => $material->id,
                            'teacher_id' => $teacher->id,
                            'class_section_id' => $classSection->id,
                        ],
                        [
                            'academic_year_id' => $academicYearId,
                            'term_id' => $termId,
                            'material_id' => $material->id,
                            'teacher_id' => $teacher->id,
                            'class_section_id' => $classSection->id,
                        ]
                    );

                    $this->command->info("تم تعيين معلم ID: {$teacher->id} لمادة ID: {$material->id} في شعبة ID: {$classSection->id} للمدرسة ID: {$school->id}");
                }
            }
        }
    }
}
