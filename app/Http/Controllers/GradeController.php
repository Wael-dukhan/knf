<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;


class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::with('academicYear')->get();
        // dd($grades);
        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        $schools = School::all();
        $academicYears = AcademicYear::all();

        // المراحل الدراسية الثلاث
        $gradeLevels  = Grade::GRADE_LEVELS;

        return view('grades.create', compact('schools', 'academicYears', 'gradeLevels'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('grades')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id)
                                ->where('academic_year_id', $request->academic_year_id)
                                ->whereNull('deleted_at'); // تجاهل السجلات المحذوفة ناعماً
                }),
            ],
            [
                'name.unique' => 'هذا الصف مسجل بالفعل في نفس المدرسة ونفس السنة الدراسية.',
            ],
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'nullable|string|max:255',
            'grade_level' => 'required|in:1,2,3', // التأكد أن القيمة تكون واحدة من 1 أو 2 أو 3
        ]);

        Grade::create($request->all());
        return redirect()->route('grade_levels.show',['school' => $request->input('school_id') , 'grade_level' => $request->input('grade_level')])->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit(Grade $grade)
    {
        $academicYears = AcademicYear::all();
        return view('grades.edit', compact('grade', 'academicYears'));
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'nullable|string|max:255',
            'grade_level' => 'required|in:1,2,3', // التأكد أن القيمة تكون واحدة من 1 أو 2 أو 3
        ]);

        $grade->update($request->all());
        return redirect()->route('admin.grades.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('grade_levels.show',[$grade->school->id , $grade->grade_level])->with('success', 'تم الحذف بنجاح');
    }
    
    public function show(Grade $grade)
    {
        // تحميل الشعب مع عد الطلاب في كل شعبة
        $grade->load('classSections','academicYear');
    
        foreach ($grade->classSections as $section) {
            $section->loadCount(['users as student_count' => function ($query) {
                $query->whereHas('roles', fn($q) => $q->where('name', 'student'));
            }]);
        }
    
        // حساب العدد الإجمالي للطلاب في جميع الشعب التابعة لهذا الصف
        $totalStudents = $grade->classSections->sum('student_count');
    
        return view('grades.show', compact('grade', 'totalStudents'));
    }
    
    
}
