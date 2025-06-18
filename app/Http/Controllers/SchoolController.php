<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    // عرض قائمة المدارس
    public function index()
    {
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            $schools = School::all();
            return view('admin.schools.index', compact('schools'));
        }else if ($role === 'school_manager') {
            return redirect()->route('grade_levels.index', ['school' => $user->school_id])->with('success', 'تم عرض المدارس بنجاح.');
        } else if ($role === 'teacher' || $role === 'quran_teacher') {
            // في حالة المعلم أو معلم القرآن، يمكن عرض المدارس التي يعملون بها
            $schools = School::whereHas('teachers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
            return view('admin.schools.index', compact('schools'));
        } else if ($role === 'student' || $role === 'parent') {
            // في حالة الطالب أو ولي الأمر، يمكن عرض المدارس التي ينتمون إليها
            $schools = School::whereHas('students', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
            return view('admin.schools.index', compact('schools'));
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض المدارس التي يشرف عليها
            // $schools = School::whereHas('quranSupervisors', function ($query) use ($user) {
            //     $query->where('user_id', $user->id);
            // })->get();
            // return view('admin.schools.index', compact('schools'));
            return redirect()->route('quran-levels.index', ['school' => $user->school_id])->with('success', 'تم العرض بنجاح.');
        }

        return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
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

        $academicYears = $school->academicYears()->select('id', 'name')->get();

        return response()->json($academicYears);
    }

}
