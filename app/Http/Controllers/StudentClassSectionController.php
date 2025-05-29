<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSection;
use App\Models\User;
use App\Models\Grade;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class StudentClassSectionController extends Controller
{
    public function index($classSectionId)
    {
        $classSection = ClassSection::findOrFail($classSectionId);
        $students = $classSection->students; // الحصول على الطلاب المسجلين في الشعبة

        return view('class_section.students.index', compact('classSection', 'students'));
    }

    public function create($gradeId)
    {
        // جلب الصف الدراسي (Grade) باستخدام ID فقط
        $grade = Grade::findOrFail($gradeId);
    
        // جلب school_id عبر العلاقة مع grade
        // $schoolId = $grade->school->id;
        $school = $grade->school;
        // dd($grade->school);
        // جلب جميع الشُعب (ClassSections) التي تتبع نفس الصف الدراسي والمدرسة
        $classSections = ClassSection::where('grade_id', $gradeId)
                                    ->get();
    
        // جلب الطلاب الذين لم يتم تعيينهم لأي شعبة
        $students = User::where('school_id', $school->id)
                        ->whereHas('roles', function($query) {
                            $query->where('name', 'student');
                        })
                        ->whereDoesntHave('classSections')  // هذا السطر يقوم بتصفية الطلاب الذين ليس لديهم شُعب
                        ->get();
    
        return view('student_assign.create', compact('school', 'gradeId', 'classSections', 'students'));
    }
    
    public function edit($studentId)
    {
        $student = User::role('student')->with('classSections.grade')->findOrFail($studentId);
        
        // نفترض أنه مسجل في شعبة واحدة فقط
        $currentClassSection = $student->classSections->first();
        
        // جلب كل الشعب لنفس المدرسة أو الصف
        $availableClassSections = ClassSection::whereHas('grade', function ($query) use ($currentClassSection) {
            $query->where('school_id', $currentClassSection->grade->school_id);
        })->get();

        return view('student_assign.edit', compact('student', 'currentClassSection', 'availableClassSections'));
    }

    
    public function update(Request $request, $studentId)
    {
        $request->validate([
            'class_section_id' => 'required|exists:class_sections,id',
        ]);

        $student = User::role('student')->findOrFail($studentId);

        // نفترض أن الطالب مسجل في شعبة واحدة فقط
        $currentSection = $student->classSections()->first();

        // إزالة من الشعبة القديمة
        if ($currentSection) {
            $student->classSections()->detach($currentSection->id);
        }

        // إضافة إلى الشعبة الجديدة
        $student->classSections()->attach($request->class_section_id);

        return redirect()->route('admin.class_sections.show', $request->class_section_id)
            ->with('success', 'تم نقل الطالب إلى الشعبة الجديدة بنجاح.');
    }


    public function assign(Request $request)
    {
        // التحقق من أن هناك طلاب تم إرسالهم
        $this->validate($request, [
            'class_section_id' => 'required|exists:class_sections,id',
            'student_id' => 'required|array',  // التحقق من أن student_id هو مصفوفة
            'student_id.*' => 'exists:users,id',  // التأكد أن كل id طالب موجود في قاعدة البيانات
        ]);
    
        // جلب الشعبة المختارة
        $classSection = ClassSection::findOrFail($request->class_section_id);
    
        // جلب الطلاب المختارين
        $studentIds = $request->student_id;
    
        // إضافة الطلاب إلى الشعبة
        $classSection->users()->attach($studentIds);
    
        return redirect()->route('admin.class_sections.show', $classSection->id)
            ->with('success', __('messages.assigned_successfully'));
    }
    

    


    // public function store(Request $request, $classSectionId)
    // {
    //     $request->validate([
    //         'student_id' => 'required|exists:users,id',
    //     ]);

    //     $classSection = ClassSection::findOrFail($classSectionId);
    //     $student = User::findOrFail($request->student_id);

    //     // إضافة الطالب إلى الشعبة
    //     $classSection->students()->attach($student);

    //     return redirect()->route('class_section.students.index', $classSectionId)
    //         ->with('success', 'تم إضافة الطالب إلى الشعبة بنجاح');
    // }

    public function destroy($classSectionId, $studentId)
    {
        // تنفيذ soft delete يدويًا في الجدول الوسيط
        DB::table('student_class_section')
            ->where('class_section_id', $classSectionId)
            ->where('user_id', $studentId)
            ->update(['deleted_at' => now()]);

        return redirect()->route('admin.class_sections.show', $classSectionId)
            ->with('success', 'تم حذف الطالب من الشعبة بنجاح');
    }


}
