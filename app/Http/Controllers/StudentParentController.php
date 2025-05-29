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
        $students = User::role('student')->get(); // استرجاع الطلاب
        // dd($students);
        return view('students.parents.index', compact('students'));
    }

    // عرض نموذج إضافة أولياء الأمور للطلاب
    public function create()
    {
        $students = User::role('student')->get(); // العثور على الطالب
        $parents = User::role('parent')->get(); // استرجاع أولياء الأمور
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
        $student = User::findOrFail($studentId); // العثور على الطالب
        $parents = User::role('parent')->get(); 
        $parent = User::role('parent')->findOrFail($parentId);

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
