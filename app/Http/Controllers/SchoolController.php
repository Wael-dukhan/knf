<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    // عرض قائمة المدارس
    public function index()
    {
        $schools = School::all();
        return view('admin.schools.index', compact('schools'));
    }

    // عرض صفحة إضافة مدرسة جديدة
    public function create()
    {
        return view('admin.schools.create');
    }

    // تخزين بيانات المدرسة
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => [
                'required', 'string', 'max:255',
                Rule::unique('schools')->where(fn ($query) =>
                    $query->where('name', $request->name)
                          ->whereNull('deleted_at') // لمنع التكرار بين السجلات غير المحذوفة فقط
                ),
            ],
        ]);

        School::create($request->only(['name', 'location']));

        return redirect()->route('admin.schools.index')->with('success', 'تم إنشاء المدرسة بنجاح.');
    }

    // عرض صفحة تعديل المدرسة
    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    // تحديث بيانات المدرسة
    public function update(Request $request, School $school)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => [
                'required', 'string', 'max:255',
                Rule::unique('schools')->ignore($school->id)->where(fn ($query) =>
                    $query->where('name', $request->name)
                          ->whereNull('deleted_at')
                ),
            ],
        ]);

        $school->update($request->only(['name', 'location']));

        return redirect()->route('admin.schools.index')->with('success', 'تم تحديث بيانات المدرسة بنجاح.');
    }

    // حذف المدرسة (Soft Delete)
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('admin.schools.index')->with('success', 'تم حذف المدرسة بنجاح.');
    }

    // عرض تفاصيل المدرسة
    public function show(School $school)
    {
        return view('admin.schools.show', compact('school'));
    }

    // عرض المراحل الدراسية للمدرسة
    public function gradeLevels(School $school)
    {
        $gradeLevels = Grade::GRADE_LEVELS;
        return view('grade_levels.index', compact('school', 'gradeLevels'));
    }

    public function getAcademicYears($schoolId)
    {
        $school = School::find($schoolId);

        if (!$school) {
            return response()->json(['error' => 'School not found'], 404);
        }

        $academicYears = $school->academicYear()->select('id', 'name')->get();

        return response()->json($academicYears);
    }

}
