<?php 
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Grade;

class AcademicYearController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع السنوات الدراسية
            $academicYears = AcademicYear::with('school')->orderByDesc('id')->get();
            // dd($academicYears);
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض السنوات الدراسية الخاصة بالمدرسة التي يديرها
            $academicYears = AcademicYear::where('school_id', $user->school_id)->with('school')->orderByDesc('id')->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return view('academic_years.index', compact('academicYears'));
    }

    public function create()
    {
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع السنوات الدراسية
            $schools = \App\Models\School::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض السنوات الدراسية الخاصة بالمدرسة التي يديرها
            $schools = \App\Models\School::where('id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }

        return view('academic_years.create', compact('schools'));
    }
    
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         // 'name' => 'required|unique:academic_years,name|max:20',
    //         'name' => [
    //             'required',
    //             'max:20',
    //             Rule::unique('academic_years')->where(function ($query) use ($request) {
    //                 return $query->where('school_id', $request->school_id);
    //             }),
    //         ],
    //         'school_id' => 'required|exists:schools,id',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);
    
    //     AcademicYear::create([
    //         'name' => $request->name,
    //         'school_id' => $request->school_id,
    //         'start_date' => $request->start_date,
    //         'end_date' => $request->end_date,
    //     ]);
    
    //     return redirect()->route('admin.academic_years.index')->with('success', 'تمت إضافة السنة الدراسية بنجاح');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'max:20',
                Rule::unique('academic_years')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                }),
            ],
            'school_id' => 'required|exists:schools,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // إنشاء السنة الدراسية
        $academicYear = AcademicYear::create([
            'name' => $request->name,
            'school_id' => $request->school_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        // الصفوف الدراسية مع أسمائها
        $grades = [
            1 => 'الصف الأول',
            2 => 'الصف الثاني',
            3 => 'الصف الثالث',
            4 => 'الصف الرابع',
            5 => 'الصف الخامس',
            6 => 'الصف السادس',
            7 => 'الصف السابع',
            8 => 'الصف الثامن',
            9 => 'الصف التاسع',
            10 => 'الصف العاشر',
            11 => 'الصف الحادي عشر',
            12 => 'البكالوريا',
        ];

        foreach ($grades as $number => $name) {
            $gradeLevel = $this->determineGradeLevel($number);

            if ($gradeLevel === 3) {
                // ثانوي: أنشئ علمي وأدبي
                foreach (['science' => 'علمي', 'literary' => 'أدبي'] as $trackKey => $trackLabel) {
                    Grade::create([
                        'name' => $name . ' - ' . $trackLabel,
                        'grade_number' => $number,
                        'description' => 'وصف ' . $name . ' - ' . $trackLabel,
                        'school_id' => $request->school_id,
                        'academic_year_id' => $academicYear->id,
                        'grade_level' => $gradeLevel,
                        'track' => $trackKey,
                    ]);
                }
            } else {
                
                // ابتدائي أو إعدادي
                Grade::create([
                    'name' => $name,
                    'grade_number' => $number,
                    'description' => 'وصف ' . $name,
                    'school_id' => $request->school_id,
                    'academic_year_id' => $academicYear->id,
                    'grade_level' => $gradeLevel,
                    'track' => null,
                ]);
            }
        }

        return redirect()->route('admin.academic_years.index')->with('success', 'تمت إضافة السنة الدراسية وجميع الصفوف بنجاح.');
    }

    // دالة تحديد المرحلة بناءً على رقم الصف
    private function determineGradeLevel($gradeNumber)
    {
        if ($gradeNumber >= 1 && $gradeNumber <= 4) {
            return 1; // ابتدائي
        } elseif ($gradeNumber >= 5 && $gradeNumber <= 9) {
            return 2; // إعدادي
        } else {
            return 3; // ثانوي
        }
    }

    public function edit(AcademicYear $academic_year)
    {
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع السنوات الدراسية
            $schools = \App\Models\School::all();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض السنوات الدراسية الخاصة بالمدرسة التي يديرها
            $schools = \App\Models\School::where('id', $user->school_id)->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        return view('academic_years.edit', compact('academic_year', 'schools'));
    }
    

    // public function update(Request $request, AcademicYear $academic_year)
    // {
    //     $request->validate([
    //         'name' => 'required|max:20|unique:academic_years,name,' . $academic_year->id,
    //         'school_id' => 'required|exists:schools,id',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);
    
    //     $academic_year->update($request->only(['name', 'school_id', 'start_date', 'end_date']));
    
    //     return redirect()->route('admin.academic_years.index')->with('success', 'تم التحديث بنجاح');
    // }

    public function update(Request $request, AcademicYear $academic_year)
    {
        $request->validate([
            'name' => [
                'required',
                'max:20',
                Rule::unique('academic_years', 'name')->ignore($academic_year->id)->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                }),
            ],
            'school_id' => 'required|exists:schools,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // منع وجود أكثر من سنة دراسية نشطة لنفس المدرسة (باستثناء هذه السنة نفسها)
        if ($request->status === 'active') {
            $existsActive = AcademicYear::where('school_id', $request->school_id)
                ->where('status', 'active')
                ->where('id', '!=', $academic_year->id)
                ->exists();

            if ($existsActive) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['status' => 'هناك سنة دراسية نشطة أخرى لهذه المدرسة.']);
            }
        }

        // التحديث
        $academic_year->update([
            'name' => $request->name,
            'school_id' => $request->school_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.academic_years.index')->with('success', 'تم التحديث بنجاح.');
    }

    
    public function destroy(AcademicYear $academic_year)
    {
        $academic_year->delete();

        return redirect()->route('admin.academic_years.index')->with('success', 'تم الحذف بنجاح');
    }


}
