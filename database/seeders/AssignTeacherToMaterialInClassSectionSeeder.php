<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\User;
use App\Models\ClassSection;
use App\Models\MaterialTeacherAssignment;
use Illuminate\Database\Seeder;

class AssignTeacherToMaterialInClassSectionSeeder extends Seeder
{
    public function run()
    {
        $academicYearId = 1; // تأكد أنه موجود في جدول academic_years
        $termId = 1;         // تأكد أنه موجود في جدول terms

        // اختيار معلم ومادة وشعبة
        $teacher = User::role('teacher')->where('school_id', 1)->first();
        $classSection = ClassSection::where('grade_id', 1)->first();
        $material = Material::where('grade_id', 1)->first();

        if ($teacher && $material && $classSection) {
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
        } else {
            echo "⚠️ لم يتم العثور على معلم، مادة أو شعبة دراسية في قاعدة البيانات.";
        }
    }
}
