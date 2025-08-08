<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Grade;
use App\Models\Material;
use App\Models\ClassSection;

class ReportController extends Controller
{
    /**
     * تقرير علامات الطالب في الفصل
     */
    public function studentMarksReport(Request $request)
    {
        $schoolId = $request->school_id ?? null;
        $academicYearId = $request->academic_year_id ?? null;
        $termId = $request->term_id ?? null;
        $gradeId = $request->grade_id ?? null;
        $classSectionId = $request->class_section_id ?? null;
        $materialId = $request->material_id ?? null;
        $studentId = $request->student_id ?? null;

        $user = Auth::user();

        $query = DB::table('marks')
            ->join('users', 'marks.student_id', '=', 'users.id')
            ->join('materials', 'marks.material_id', '=', 'materials.id')
            ->join('class_sections', 'marks.class_section_id', '=', 'class_sections.id')
            ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
            ->join('schools', 'grades.school_id', '=', 'schools.id') // انضمام المدارس
            ->join('academic_years', 'marks.academic_year_id', '=', 'academic_years.id')
            ->join('terms', 'marks.term_id', '=', 'terms.id')
            ->select(
                'users.name as student_name',
                'schools.name as school_name',
                'grades.name as grade_name',
                'materials.name as material_name',
                'academic_years.name as academic_year_name',
                'terms.name as term_name',
                'marks.oral_mark',
                'marks.homework_mark',
                'marks.first_study_mark',
                'marks.second_study_mark',
                'marks.work_total',
                'marks.oral_exam_mark',
                'marks.written_exam_mark',
                'marks.term_total'
            );

        if ($user->hasRole('teacher')) {
            $teacherSections = DB::table('class_section_teachers')
                ->where('teacher_id', $user->id)
                ->pluck('class_section_id')
                ->toArray();

            $query->whereIn('marks.class_section_id', $teacherSections);
        }

        if ($schoolId) $query->where('schools.id', $schoolId);
        if ($academicYearId) $query->where('marks.academic_year_id', $academicYearId);
        if ($termId) $query->where('marks.term_id', $termId);
        if ($gradeId) $query->where('grades.id', $gradeId);
        if ($classSectionId) $query->where('grades.id', $classSectionId);
        if ($materialId) $query->where('marks.material_id', $materialId);
        if ($studentId) $query->where('marks.student_id', $studentId);

        $marks = $query->get();

        $schools = School::orderBy('name')->get();
        $academicYears = AcademicYear::with('school')->orderBy('name')->get();
        $terms = Term::with('school')->orderBy('name')->get();
        $grades = Grade::with('school')->orderBy('name')->get();
        $classSections = ClassSection::with(['grade'])->orderBy('name')->get();
        $materials = Material::orderBy('name')->get();
        // dd($classSections);
        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'student')
            ->orderBy('users.name')
            ->select('users.id', 'users.name')
            ->get();

        return view('reports.student_marks_report', compact(
            'schools', 'academicYears', 'terms', 'grades', 'materials', 'students', 'marks',
            'schoolId', 'academicYearId', 'termId', 'gradeId', 'materialId', 'studentId' , 'classSections'
        ));
    }

   public function marksReportData(Request $request)
    {
        $user = Auth::user();

        $query = DB::table('marks')
            ->join('users', 'marks.student_id', '=', 'users.id')
            ->join('materials', 'marks.material_id', '=', 'materials.id')
            ->join('class_sections', 'marks.class_section_id', '=', 'class_sections.id')
            ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
            ->join('schools', 'grades.school_id', '=', 'schools.id')
            ->join('academic_years', 'marks.academic_year_id', '=', 'academic_years.id')
            ->join('terms', 'marks.term_id', '=', 'terms.id')
            ->select(
                'marks.id',
                'users.name as student_name',
                'schools.name as school_name',
                'grades.name as grade_name',
                'class_sections.name as class_section_name',  // إضافة الشعبة الدراسية هنا
                'materials.name as material_name',
                'academic_years.name as academic_year_name',
                'terms.name as term_name',
                'marks.oral_mark',
                'marks.homework_mark',
                'marks.first_study_mark',
                'marks.second_study_mark',
                'marks.work_total',
                'marks.oral_exam_mark',
                'marks.written_exam_mark',
                'marks.term_total'
            );

        if ($user->hasRole('teacher')) {
            $teacherSections = DB::table('class_section_teachers')
                ->where('teacher_id', $user->id)
                ->pluck('class_section_id')
                ->toArray();

            $query->whereIn('marks.class_section_id', $teacherSections);
        }
        // dd($query->toSql());
        if ($request->filled('school_id')) $query->where('schools.id', $request->school_id);
        if ($request->filled('academic_year_id')) $query->where('marks.academic_year_id', $request->academic_year_id);
        if ($request->filled('term_id')) $query->where('marks.term_id', $request->term_id);
        if ($request->filled('grade_id')) $query->where('grades.id', $request->grade_id);
        if ($request->filled('class_section_id')) $query->where('class_sections.id', $request->class_section_id);
        if ($request->filled('material_id')) $query->where('marks.material_id', $request->material_id);
        if ($request->filled('student_id')) $query->where('marks.student_id', $request->student_id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }

    public function getClassSectionsByGrade($gradeId)
    {
        $sections = ClassSection::where('grade_id', $gradeId)->orderBy('name')->get();
        return response()->json($sections);
    }

    /**
     * تقرير المحصلة السنوية للطالب
     */
    public function annualReport()
    {
        return view('reports.annual_report');
    }

    /**
     * تقرير ترتيب الطالب داخل الصف
     */
    public function classRanking()
    {
        return view('reports.class_ranking');
    }

    /**
     * تقرير الحضور والغياب
     */
    public function attendance()
    {
        return view('reports.attendance');
    }

    /**
     * التقرير العام لأداء الطالب
     */
    public function overallPerformance()
    {
        return view('reports.overall_performance');
    }

    public function getAcademicYears($schoolId)
    {
        return DB::table('academic_years')
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get();
    }

    public function getTerms($yearId)
    {
        return DB::table('terms')
            ->where('academic_year_id', $yearId)
            ->orderBy('name')
            ->get();
    }

    public function getMaterials($yearId)
    {
        return DB::table('materials')
            ->where('academic_year_id', $yearId)
            ->orderBy('name')
            ->get();
    }

    public function getStudents($yearId)
    {
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', function ($query) {
                $query->select('id')->from('roles')->where('name', 'student')->limit(1);
            })
            ->where('users.academic_year_id', $yearId)
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();
    }
    public function getGradesBySchool($schoolId)
    {
        $grades = DB::table('grades')
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        return response()->json($grades);
    }

    public function getStudentsBySchool($schoolId)
    {
        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('student_guardians', 'users.id', '=', 'student_guardians.student_id')
            ->join('grades', 'student_guardians.grade_id', '=', 'grades.id')
            ->where('roles.name', 'student')
            ->where('grades.school_id', $schoolId)
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        return response()->json($students);
    }

    public function getMaterialsByGrade($gradeId)
    {
        $materials = DB::table('materials')
            ->join('grade_material', 'materials.id', '=', 'grade_material.material_id')
            ->where('grade_material.grade_id', $gradeId)
            ->select('materials.id', 'materials.name')
            ->orderBy('materials.name')
            ->get();

        return response()->json($materials);
    }

}
