<?php

namespace App\Http\Controllers;

use App\Models\QuranClass;
use App\Models\QuranLevel;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\User;

class QuranClassesController extends Controller
{
    // عرض جميع الحلقات
    public function index()
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع الحلقات
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع الحلقات
            $quranClasses = QuranClass::with(['quranLevel.school', 'quranTeacher', 'students'])->get();
            $schools = School::all();
            $teachers = User::role('quran_teacher')->get();
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض الحلقات الخاصة بالمدرسة التي يديرها
            $quranClasses = QuranClass::whereHas('quranLevel.school', function($query) use ($user) {
                $query->where('id', $user->school_id);
            })->with(['quranLevel.school', 'quranTeacher', 'students'])->get();
            $schools = School::where('id', $user->school_id)->get();
            $teachers = User::role('quran_teacher')->where('school_id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return view('quran-classes.index', compact('quranClasses', 'schools', 'teachers'));
    }


    // صفحة إنشاء حلقة جديدة
    public function create()
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع مستويات القرآن
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع مستويات القرآن
            $quranLevels = QuranLevel::all();
            $teachers = User::role('quran_teacher')->get();
            $schools = School::all();
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض مستويات القرآن الخاصة بالمدرسة التي يديرها
            $quranLevels = QuranLevel::whereHas('school', function($query) use ($user) {
                $query->where('id', $user->school_id);
            })->get();
            $teachers = User::role('quran_teacher')->where('school_id', $user->school_id)->get();
            $schools = School::where('id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // dd($quranLevels);
        return view('quran-classes.create', compact('quranLevels', 'teachers', 'schools'));
    }

    // حفظ حلقة جديدة
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'quran_level_id' => 'required|exists:quran_levels,id',
            'teacher_id' => 'required|exists:users,id', // Assuming teachers are users
        ]);

        // إذا التحقق تم بنجاح، ننشئ الحلقة القرآنية
        $quranClass = QuranClass::create($validatedData);

        return redirect()->route('quran-classes.index')
                        ->with('success', __('Quran class created successfully.'));
    }


    // عرض تفاصيل حلقة معينة
    public function show(QuranClass $quranClass)
    {   
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض تفاصيل الحلقة
        if ($role === 'super_admin' || $role === 'quran_teacher') {
            // في حالة المشرف العام، يمكن عرض تفاصيل الحلقة
            $quranClass->load('quranTeacher', 'students', 'quranLevel');
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض تفاصيل الحلقة الخاصة بالمدرسة التي يديرها
            if ($quranClass->quranLevel->school_id !== $user->school_id) {
                return redirect()->route('quran-classes.index')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الحلقة.');
            }
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // dd($quranClass);
        return view('quran-classes.show', compact('quranClass','role'));
    }

    // صفحة تعديل حلقة
    public function edit(QuranClass $quranClass)
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض تفاصيل الحلقة
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض تفاصيل الحلقة
            $quranLevel = QuranLevel::where('school_id', $quranClass->quranLevel->school_id)
                                    ->get();
            $teachers = User::role('quran_teacher')->where('school_id' , $quranClass->quranLevel->school_id)->get();
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض تفاصيل الحلقة الخاصة بالمدرسة التي يديرها
            if ($quranClass->quranLevel->school_id !== $user->school_id) {
                return redirect()->route('quran-classes.index')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الحلقة.');
            }
            $quranLevel = QuranLevel::where('school_id', $user->school_id)->get();
            $teachers = User::role('quran_teacher')->where('school_id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        
        return view('quran-classes.edit', compact('quranClass', 'quranLevel', 'teachers'));
    }

    // تحديث بيانات حلقة
    public function update(Request $request, QuranClass $quranClass)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quran_level_id' => 'required|exists:quran_levels,id',
            'teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string|max:1000',
        ]);

        $quranClass->update($validatedData);

        return redirect()->route('quran-levels.show' , $quranClass->quranLevel->id)->with('success', 'تم تحديث بيانات الحلقة بنجاح');
;
    }

    // حذف حلقة
    public function destroy(QuranClass $quranClass)
    {
        $quranClass->delete();
        return redirect()->route('quran-classes.index')->with('success', 'Quran class deleted successfully.');
    }

    // إضافة طالب إلى حلقة
    public function addStudent(Request $request, QuranClass $quranClass)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // تحقق مما إذا كان الطالب مسجلاً بالفعل في هذه الحلقة
        if ($quranClass->isStudentEnrolled($request->student_id)) {
            return redirect()->back()->with('error', 'Student is already enrolled in this class.');
        }

        // إضافة الطالب إلى الحلقة
        $quranClass->students()->attach($request->student_id, ['status' => 'active']);

        return redirect()->route('quran-classes.show', $quranClass->id)
                        ->with('success', 'Student added to the Quran class successfully.');
    }
    // إزالة طالب من حلقة
    public function removeStudent(Request $request, QuranClass $quranClass)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // تحقق مما إذا كان الطالب مسجلاً في هذه الحلقة
        if (!$quranClass->isStudentEnrolled($request->student_id)) {
            return redirect()->back()->with('error', 'Student is not enrolled in this class.');
        }

        // إزالة الطالب من الحلقة
        $quranClass->students()->detach($request->student_id);

        return redirect()->route('quran-classes.show', $quranClass->id)
                        ->with('success', 'Student removed from the Quran class successfully.');
    }

    public function assignStudentsForm(QuranClass $quranClass)
    {
        // جلب الطلاب الذين ينتمون لنفس المدرسة والمستوى القرآني المرتبط بالحلفة
        $schoolId = $quranClass->quranLevel->school_id;  // افتراضياً الحلقة مرتبطة بمستوى يحتوي على مدرسة
        // الطلاب الذين ينتمون للمدرسة، لديهم دور student، ولم يتم تعيينهم لهذه الحلقة (باستثناء soft deleted)
        $students = User::role('student')
                        ->where('school_id', $schoolId)
                        ->whereDoesntHave('studentQuranClasses', function($query) use ($quranClass) {
                            $query->where('quran_class_id', $quranClass->id);
                        })
                        ->get();

        return view('quran-classes.assign-students', compact('quranClass', 'students'));
    }

    // تعيين طلاب لحلقة قرآنية
    public function assignStudents(Request $request, QuranClass $quranClass)
    {
        // dd($request->all() , $quranClass);
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id', // التأكد من أن كل id طالب موجود في قاعدة البيانات
        ]);

        // إضافة الطلاب إلى الحلقة
        foreach ($request->student_ids as $studentId) {
            if (!$quranClass->isStudentEnrolled($studentId)) {
                $quranClass->students()->attach($studentId, [
                    'status' => 'active',
                    'joined_at' => now(),
                    'quran_level_id' => $quranClass->quran_level_id, // افتراضياً نربط المستوى القرآني
                ]);
            }
        }
        return redirect()->route('quran-classes.show', $quranClass->id)
                        ->with('success', 'Students assigned to the Quran class successfully.');
    }

    public function getQuranTeachersBySchool($schoolId)
    {
        $teachers = User::role('quran_teacher')
            ->where('school_id', $schoolId)
            ->select('id', 'name')
            ->get();
        // dd($schoolId);
        return response()->json($teachers);
    }

}
