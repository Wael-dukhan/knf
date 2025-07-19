<?php

namespace App\Http\Controllers;

use App\Models\QuranLevel;
use App\Models\School;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuranLevelController extends Controller
{
    public function index()
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع مستويات القرآن
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع مستويات القرآن
            $quranLevels = QuranLevel::with(['school', 'academicYear'])->get();
        } else if ($role === 'quran_supervisor') {
            // في حالة مدير المدرسة، يمكن عرض مستويات القرآن الخاصة بالمدرسة التي يديرها
            $quranLevels = QuranLevel::where('school_id', $user->school_id)
                ->with(['school', 'academicYear'])
                ->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        return view('quran-levels.index', compact('quranLevels'));
    }

    public function create()
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم  
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع المدارس والسنة الدراسية
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع المدارس والسنة الدراسية
            $schools = School::all();
            $academicYears = AcademicYear::all();
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض المدارس الخاصة بالمدرسة التي يديرها
            $schools = School::where('id', $user->school_id)->get();
            $academicYears = AcademicYear::where('school_id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return view('quran-levels.create', compact('schools', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('quran_levels')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id)
                                 ->where('academic_year_id', $request->academic_year_id)
                                 ->whereNull('deleted_at');
                }),
            ],
            'level_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('quran_levels')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id)
                                ->where('academic_year_id', $request->academic_year_id)
                                ->whereNull('deleted_at');
                }),
            ],
            'description' => 'nullable|string|max:500',
            'school_id' => 'required|exists:schools,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ], [
            'name.unique' => 'هذا المستوى مسجل بالفعل في نفس المدرسة ونفس السنة الدراسية.',
        ]);

        QuranLevel::create($request->all());

        return redirect()->route('quran-levels.index')->with('success', 'تمت إضافة المستوى بنجاح');
    }

    public function edit(QuranLevel $quranLevel)
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم  
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع المدارس والسنة الدراسية
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع المدارس والسنة الدراسية
            $schools = School::all();
            $academicYears = AcademicYear::all();
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض المدارس الخاصة بالمدرسة التي يديرها
            $schools = School::where('id', $user->school_id)->get();
            $academicYears = AcademicYear::where('school_id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return view('quran-levels.edit', compact('quranLevel', 'schools', 'academicYears'));
    }

    public function update(Request $request, QuranLevel $quranLevel)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('quran_levels')->where(function ($query) use ($request, $quranLevel) {
                    return $query->where('school_id', $request->school_id)
                                 ->where('academic_year_id', $request->academic_year_id)
                                 ->whereNull('deleted_at')
                                 ->where('id', '!=', $quranLevel->id);
                }),
            ],
            'level_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('quran_levels')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id)
                                ->where('academic_year_id', $request->academic_year_id)
                                ->whereNull('deleted_at');
                }),
            ],
            'description' => 'nullable|string|max:500',
            'school_id' => 'required|exists:schools,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $quranLevel->update($request->all());

        return redirect()->route('quran-levels.index')->with('success', 'تم تحديث المستوى بنجاح');
    }

    public function destroy(QuranLevel $quranLevel)
    {
        $quranLevel->delete();
        return redirect()->route('quran-levels.index')->with('success', 'تم حذف المستوى بنجاح');
    }

    public function show(QuranLevel $quranLevel)
    {
        // التحقق من صلاحية المستخدم
        $user = auth()->user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // في حالة المشرف العام، يمكن عرض جميع مستويات القرآن
        if ($role === 'super_admin') {
            $quranLevel->load(['quranClasses.quranTeacher', 'academicYear']);
            // dd($quranLevel);
            // حساب عدد الطلاب في كل حلقة داخل هذا المستوى
            foreach ($quranLevel->quranClasses as $class) {
                $class->loadCount(['students as student_count']);
            }

            $totalStudents = $quranLevel->quranClasses->sum('student_count');
        } else if ($role === 'quran_supervisor') {
            // في حالة مشرف القرآن، يمكن عرض مستويات القرآن الخاصة بالمدرسة التي يديرها
            if ($quranLevel->school_id !== $user->school_id) {
                return redirect()->route('quran-levels.index')->with('error', 'ليس لديك صلاحية الوصول إلى هذا المستوى.');
            }
            $quranLevel->load(['quranClasses.quranTeacher', 'academicYear']);
            // حساب عدد الطلاب في كل حلقة داخل هذا المستوى
            foreach ($quranLevel->quranClasses as $class) {
                $class->loadCount(['students as student_count']);
            }

            $totalStudents = $quranLevel->quranClasses->sum('student_count');
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        

        return view('quran-levels.show', compact('quranLevel', 'totalStudents'));
    }

    public function getQuranLevelsBySchool(Request $request, $schoolId)
    {
        $quranLevels = QuranLevel::where('school_id', $schoolId)
            ->with(['academicYear'])
            ->get();
        $schoolName = School::findOrFail($schoolId)->name;

        return view('quran-levels.by-school', compact('quranLevels', 'schoolName'));
    }

    public function myLevelsWithClasses()
    {
        $user = auth()->user();

        // جلب حلقات المعلم مع تحميل المستوى المرتبط بكل حلقة
        $quranClasses = $user->quranClasses()->with('quranLevel')->get();
        // تجميع الحلقات حسب المستوى
        $levelsWithClasses = $quranClasses->groupBy(function ($class) {
            return $class->quranLevel->id;
        });
        
        // تجهيز مصفوفة المستويات مع الحلقات
        $levels = $levelsWithClasses->map(function ($classes, $levelId) {
            return [
                'level' => $classes->first()->quranLevel,
                'classes' => $classes,
            ];
        })->values();
        
        // dd($levelsWithClasses);
        return view('quran-levels.my-levels-with-classes', compact('levels'));
    }


    // في QuranLevelController.php

    public function getJsonQuranLevelsBySchool(Request $request)
    {
        $schoolId = $request->get('schoolId');
        // dd($schoolId);
        if (!$schoolId) {
            return response()->json([], 400); // خطأ لأن معرف المدرسة غير موجود
        }

        $levels = QuranLevel::where('school_id', $schoolId)
                            ->select('id', 'name')
                            ->get();

        return response()->json($levels);
    }

}
