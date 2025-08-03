<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Grade;
use App\Models\School;
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClassSectionController extends Controller
{
    public function index()
    {
        $sections = ClassSection::with('grade')->latest()->get();
        return view('class_sections.index', compact('sections'));
    }

    public function create()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        if ($role === 'super_admin') {
            // جلب السنوات الدراسية النشطة مع الصفوف المرتبطة بكل سنة
            $academicYears = AcademicYear::where('status', 'active')
                ->with('grades') // يجب تعريف علاقة grades في نموذج AcademicYear
                ->get();

            $schools = School::all();

        } elseif ($role === 'school_manager') {
            // جلب السنوات الدراسية النشطة مع الصفوف التابعة لمدرسة المدير فقط
            $academicYears = AcademicYear::where('status', 'active')
                ->with(['grades' => function ($query) use ($user) {
                    $query->where('school_id', $user->school_id);
                }])
                ->get();

            $schools = School::where('id', $user->school_id)->get();

        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return view('class_sections.create', compact('academicYears', 'schools'));
    }

    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         // 'name' => 'required|string|max:125',
    //         'name' => [
    //             'required',
    //             Rule::unique('grades')->where(function ($query) use ($request) {
    //                 return $query->where('id', $request->grade_id)
    //                             ->where('school_id', $request->school_id)
    //                             ->whereNull('deleted_at'); // تجاهل السجلات المحذوفة ناعماً
    //             }),
    //         ],
    //         [
    //             'name' => 'هذا الصف مسجل بالفعل في نفس المدرسة ونفس السنة الدراسية.',
    //         ],
    //         'grade_id' => 'required|exists:grades,id',
    //         'school_id' => 'required|exists:schools,id',
    //     ]);
    
    //     ClassSection::create($request->only(['name', 'grade_id', 'school_id']));
    
    //     return redirect()->route('admin.grades.show',$request->grade_id)->with('success', 'تمت إضافة الشعبة بنجاح');
    // }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'grade_id' => 'required|exists:grades,id',
        ], [
            'name.required' => 'يرجى إدخال اسم الشعبة.',
            'grade_id.required' => 'يرجى تحديد الصف.',
        ]);

        // الحصول على school_id من الصف
        $grade = Grade::withTrashed()->findOrFail($request->grade_id); // if soft deletes enabled
        $schoolId = $grade->school_id;

        // التحقق من وجود شعبة بنفس الاسم في نفس الصف ونفس المدرسة
        $exists = ClassSection::where('name', $request->name)
            ->where('grade_id', $request->grade_id)
            ->whereHas('grade', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereNull('deleted_at') // إذا كنت تستخدم soft deletes
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['name' => 'هذا الاسم مستخدم بالفعل ضمن نفس الصف ونفس المدرسة.'])->withInput();
        }

        // إنشاء الشعبة
        ClassSection::create([
            'name' => $request->name,
            'grade_id' => $request->grade_id,
        ]);

        return redirect()->route('admin.grades.show', $request->grade_id)
                        ->with('success', 'تمت إضافة الشعبة بنجاح');
    }

    public function edit(ClassSection $class_section)
    {
        // $grades = Grade::all();
        // $schools = School::all();
         // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الصفوف
        if ($role === 'super_admin') {
            $grades = Grade::all();
            $schools = School::all(); // إذا كنت تريد تحديد المدرسة أيضًا
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الصفوف الخاصة بالمدرسة التي يديرها
            $grades = Grade::where('school_id', $user->school_id)->get();
            $schools = School::where('id', $user->school_id)->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        return view('class_sections.edit', compact('class_section', 'grades', 'schools'));
    }
    

    public function update(Request $request, ClassSection $class_section)
    {
        // dd($request->all());
        $request->validate([
            'name' => [
                'required',
                Rule::unique('grades')->where(function ($query) use ($request) {
                    return $query->where('id', $request->grade_id)
                                ->whereNull('deleted_at'); // تجاهل السجلات المحذوفة ناعماً
                }),
            ],
            [
                'name' => 'هذا الصف مسجل بالفعل في نفس المدرسة ونفس السنة الدراسية.',
            ],
            'grade_id' => 'required|exists:grades,id',
            'school_id' => 'required|exists:schools,id',
        ]);

        $class_section->update($request->only(['name', 'grade_id', 'school_id']));
    
        return redirect()->route('admin.grades.show',$request->grade_id)->with('success', 'تم تحديث الشعبة بنجاح');
    }
    
    public function destroy(ClassSection $class_section)
    {
        // dd($class_section);
        $class_section->delete();
        return redirect()->route('admin.grades.show',$class_section->grade_id)->with('success', 'تم الحذف بنجاح');
    }

    public function show(ClassSection $class_section)
    {
        // تحميل الطلاب الذين ينتمون إلى الشعبة
        // $students = $class_section->users;
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط

        $students = DB::table('student_class_section')
            ->join('users', 'student_class_section.user_id', '=', 'users.id')
            ->join('class_sections', 'student_class_section.class_section_id', '=', 'class_sections.id')
            ->where('student_class_section.class_section_id', $class_section->id) // الشعبة المختارة
            ->whereNull('student_class_section.deleted_at') // تجاهل السجلات المحذوفة ناعماً
            ->select(
                'student_class_section.*',
                'users.id as student_id',
                'users.name as student_name',
                'users.email',
                'student_class_section.status'
            )
            ->get();

        $grade = $class_section->grade;
        // dd($class_section);
        return view('class_sections.show', compact('class_section', 'students','grade','role'));
    }
    

   public function showTeacherClasses()
    {
        $user = Auth::user();

        if (!$user->hasRole('teacher')) {
            abort(403, 'ليست لديك صلاحية لعرض هذه الصفحة.');
        }

        // جلب كل الشعب المرتبطة بالمعلم عبر المواد، مع معلومات الصف وسنة الدراسة
        $classSections = DB::table('material_teacher_term_class_section as mttcs')
            ->join('class_sections as cs', 'mttcs.class_section_id', '=', 'cs.id')
            ->join('grades as g', 'cs.grade_id', '=', 'g.id')
            ->join('academic_years as ay', 'mttcs.academic_year_id', '=', 'ay.id')
            ->where('mttcs.teacher_id', $user->id)
            ->select(
                'cs.*',
                'g.name as grade_name',
                'ay.name as academic_year_name'
            )
            ->distinct()
            ->get();

        // لكل شعبة جلب الطلاب المنتمين لها
        foreach ($classSections as $section) {
            $section->students = DB::table('student_class_section as scs')
                ->join('users', 'scs.user_id', '=', 'users.id')
                ->where('scs.class_section_id', $section->id)
                ->whereNull('scs.deleted_at')
                ->select('users.id', 'users.name', 'users.email', 'scs.status')
                ->get();
        }

        return view('teacher.class_sections.index', compact('classSections'));
    }


    // ClassSectionController.php

    public function assignStudents($classSectionId)
    {
        // الحصول على الشعبة والطلاب المتاحين للتعيين
        $classSection = ClassSection::findOrFail($classSectionId);
        $students = User::role('student')->get(); // الحصول على الطلاب فقط باستخدام صلاحية الطالب

        return view('class_sections.assign', compact('classSection', 'students'));
    }

    public function storeAssignedStudents(Request $request, $classSectionId)
    {
        // التحقق من صحة المدخلات
        $request->validate([
            'students' => 'required|array',  // يجب أن يتم إرسال مصفوفة من الطلاب
            'students.*' => 'exists:users,id',  // التأكد من أن كل طالب موجود في قاعدة البيانات
        ]);

        // الحصول على الشعبة
        $classSection = ClassSection::findOrFail($classSectionId);

        // إضافة الطلاب إلى الشعبة
        $classSection->students()->sync($request->students); // sync يحافظ على تحديث العلاقة فقط

        return redirect()->route('class-sections.show', $classSectionId)
                        ->with('success', 'تم إسناد الطلاب للشعبة بنجاح.');
    }


}
