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
        // $schools = School::all();
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع المدارس
            $academicYears = AcademicYear::all();
            $schools = School::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض المدارس الخاصة بالمدرسة التي يديرها
            $schools = School::where('id', $user->school_id)->get();
            $academicYears = AcademicYear::where('school_id', $user->school_id)->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
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
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع المدارس
            $academicYears = AcademicYear::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض المدارس الخاصة بالمدرسة التي يديرها
            $academicYears = AcademicYear::where('school_id', $user->school_id)->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // $academicYears = AcademicYear::all();
        return view('grades.edit', compact('grade', 'academicYears'));
    }

    public function update(Request $request, Grade $grade)
    {
        // dd($grade);

        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'nullable|string|max:255',
            'grade_level' => 'required|in:1,2,3', // التأكد أن القيمة تكون واحدة من 1 أو 2 أو 3
        ]);

        // $grade->update($request->all());
        $grade->update([
            'name' => $request->name,
            'academic_year_id' => $request->academic_year_id,
            'description' => $request->description,
            'grade_level' => $request->grade_level,
        ]);
        return redirect()->route('grade_levels.show',['school'=>$grade->school_id , 'grade_level' => $request->grade_level])->with('success', 'تم التحديث بنجاح');
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

    public function getGradesBySchool($school_id)
    {
        $grades = Grade::where('school_id', $school_id)->select('id', 'name')->get();

        return response()->json($grades);
    }

    
    
}
