<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\ClassSection;
use App\Models\User;
use App\Models\Term;
use App\Models\Material;
class FilteredController extends Controller
{
    public function academicYears($schoolId)
    {
        return AcademicYear::where('school_id', $schoolId)->get();
    }

    public function grades($schoolId, $yearId)
    {
        return Grade::where('school_id', $schoolId)
            ->where('academic_year_id', $yearId)
            ->get();
    }

    public function classSections($gradeId)
    {
        return ClassSection::where('grade_id', $gradeId)->get();
    }

    public function students($classSectionId)
    {
        return ClassSection::find($classSectionId)->users()->role('student')->get();
        // return User::role('student')->where('class_section_id', $classSectionId)->get();
    }

    public function terms($academicYearId)
    {
        return response()->json(Term::where('academic_year_id', $academicYearId)->get());
    }

    public function materials($gradeId)
    {
        return response()->json(Material::where('grade_id', $gradeId)->get());
    }

}

