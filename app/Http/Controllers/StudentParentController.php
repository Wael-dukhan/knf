<?php

// app/Http/Controllers/StudentParentController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StudentParentController extends Controller
{
    // عرض جميع الطلاب
public function index()
{
    // التحقق من صلاحية المستخدم
    $user = auth()->user(); // الحصول على المستخدم الحالي
    // الحصول على دور المستخدم
    $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
    // في حالة المشرف العام، يمكن عرض جميع الأدوار والمدارس
    if ($role === 'super_admin') {
         $students = User::role('student')
        ->with(['parents', 'school']) // جلب المدرسة وأولياء الأمور معًا
        ->get();
    } else if ($role === 'school_manager') {
        // في حالة مدير المدرسة، يمكن عرض الطلاب الخاصين بالمدرسة التي يديرها
        $students = User::role('student')
            ->where('school_id', $user->school_id)
            ->with(['parents', 'school']) // جلب المدرسة وأولياء الأمور معًا
            ->get();
    } else if ($role === 'teacher' || $role === 'quran_teacher') {
        // في حالة المعلم أو معلم القرآن، يمكن عرض الطلاب الذين يدرسهم
        $students = User::role('student')
            ->whereHas('teachers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['parents', 'school']) // جلب المدرسة وأولياء الأمور معًا
            ->get();
    } else {
        // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
        return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
    }


    return view('students.parents.index', compact('students'));
}

    // عرض نموذج إضافة أولياء الأمور للطلاب
    public function create()
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الطلاب وأولياء الأمور
        if ($role === 'super_admin') {
            $students = User::role('student')->get(); // العثور على جميع الطلاب
            $parents = User::role('parent')->get(); // استرجاع أولياء الأمور
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الطلاب وأولياء الأمور الخاصين بالمدرسة التي يديرها
            $students = User::role('student')
                ->where('school_id', $user->school_id)
                ->get();
            $parents = User::role('parent')
                ->where('school_id', $user->school_id)
                ->get();
        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        return view('students.parents.create', compact('students', 'parents'));
    }

    // تعيين أولياء الأمور للطالب
    // تخزين البيانات (ربط الطلاب مع المعلمين)
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'parent_id' => 'required|exists:users,id',
        ]);

        $student = User::find($request->student_id);
        $parentId = $request->parent_id;

        // Many-to-Many relationship
        $student->parents()->syncWithoutDetaching([$parentId]);

        return redirect()->back()->with('success', 'Parent assigned to student successfully.');
    }

    // عرض نموذج تعديل أولياء الأمور للطلاب
    public function edit($studentId, int $parentId)
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الطلاب وأولياء الأمور
        if ($role === 'super_admin') {
            $student = User::findOrFail($studentId); // العثور على الطالب
            $parents = User::role('parent')->get(); 
            $parent = User::role('parent')->findOrFail($parentId);
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الطلاب وأولياء الأمور الخاصين بالمدرسة التي يديرها
            $student = User::role('student')
                ->where('school_id', $user->school_id)
                ->findOrFail($studentId); // العثور على الطالب
            $parents = User::role('parent')
                ->where('school_id', $user->school_id)
                ->get();
            $parent = User::role('parent')->findOrFail($parentId);
        } else {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }


        return view('students.parents.edit', compact('student', 'parent','parents'));
    }

    // تحديث أولياء الأمور للطالب
    public function update(Request $request, $studentId)
    {
        $request->validate([
            'parent_ids' => 'required|array',
            'parent_ids.*' => 'exists:users,id', // التأكد من أن جميع الآباء موجودون
        ]);

        $student = User::findOrFail($studentId); // العثور على الطالب
        $student->parents()->sync($request->parent_ids); // تحديث العلاقة بين الطالب والأولياء

        return redirect()->route('students.parents.index')->with('success', 'Parents updated successfully.');
    }

    // حذف علاقة ولي الأمر بالطالب
    public function destroy($studentId, $parentId)
    {
        $student = User::findOrFail($studentId);
        $student->parents()->detach($parentId); // إزالة العلاقة بين الطالب وولي الأمر

        return redirect()->route('students.parents.index')->with('success', 'Parent removed successfully.');
    }
}
