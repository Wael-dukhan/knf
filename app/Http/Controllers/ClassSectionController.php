<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Grade;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ClassSectionController extends Controller
{
    public function index()
    {
        $sections = ClassSection::with('grade')->latest()->get();
        return view('class_sections.index', compact('sections'));
    }

    public function create()
    {
        $grades = Grade::all();
        $schools = School::all(); // إذا كنت تريد تحديد المدرسة أيضًا
        return view('class_sections.create', compact('grades', 'schools'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            // 'name' => 'required|string|max:125',
            'name' => [
                'required',
                Rule::unique('grades')->where(function ($query) use ($request) {
                    return $query->where('grade_id', $request->grade_id)
                                ->whereNull('deleted_at'); // تجاهل السجلات المحذوفة ناعماً
                }),
            ],
            [
                'name' => 'هذا الصف مسجل بالفعل في نفس المدرسة ونفس السنة الدراسية.',
            ],
            'grade_id' => 'required|exists:grades,id',
            'school_id' => 'required|exists:schools,id',
        ]);
    
        ClassSection::create($request->only(['name', 'grade_id', 'school_id']));
    
        return redirect()->route('admin.class_sections.index')->with('success', 'تمت إضافة الشعبة بنجاح');
    }
    

    public function edit(ClassSection $class_section)
    {
        $grades = Grade::all();
        $schools = School::all();
        return view('class_sections.edit', compact('class_section', 'grades', 'schools'));
    }
    

    public function update(Request $request, ClassSection $class_section)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('grades')->where(function ($query) use ($request) {
                    return $query->where('grade_id', $request->grade_id)
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

        $students = DB::table('student_class_section')
            ->join('users', 'student_class_section.user_id', '=', 'users.id')
            ->join('class_sections', 'student_class_section.class_section_id', '=', 'class_sections.id')
            ->where('student_class_section.class_section_id', $class_section->id) // الشعبة المختارة
            ->select(
                'student_class_section.*',
                'users.id as student_id',
                'users.name as student_name',
                'users.email',
                'student_class_section.status'
            )
            ->get();

        // dd($class_section);
        $grade = $class_section->grade;
    
        return view('class_sections.show', compact('class_section', 'students','grade'));
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
