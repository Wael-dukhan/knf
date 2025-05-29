<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\School;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeLevelController extends Controller
{
    public function show($schoolId, $gradeLevelId)
    {
        $school = School::findOrFail($schoolId);

        // المراحل الثابتة
        $gradeLevelName  = Grade::GRADE_LEVELS[$gradeLevelId];
        
        $gradeLevel = new \stdClass();
        $gradeLevel->id = $gradeLevelId;
        $gradeLevel->name = $gradeLevelName;

        // جلب الصفوف التابعة للمدرسة والمرحلة الدراسية المحددة
        $grades = Grade::with('academicYear')
                    ->where('school_id', $schoolId)
                    ->where('grade_level', $gradeLevelId)
                    ->get();

        return view('grade_levels.show', compact('school', 'gradeLevel', 'grades'));
    }

}
