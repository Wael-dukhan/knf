<?php 
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
    
    public function store(Request $request)
    {
        $request->validate([
            // 'name' => 'required|unique:academic_years,name|max:20',
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
        ]);
    
        AcademicYear::create([
            'name' => $request->name,
            'school_id' => $request->school_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
    
        return redirect()->route('admin.academic_years.index')->with('success', 'تمت إضافة السنة الدراسية بنجاح');
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
    

    public function update(Request $request, AcademicYear $academic_year)
    {
        $request->validate([
            'name' => 'required|max:20|unique:academic_years,name,' . $academic_year->id,
            'school_id' => 'required|exists:schools,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
    
        $academic_year->update($request->only(['name', 'school_id', 'start_date', 'end_date']));
    
        return redirect()->route('admin.academic_years.index')->with('success', 'تم التحديث بنجاح');
    }
    
    public function destroy(AcademicYear $academic_year)
    {
        $academic_year->delete();

        return redirect()->route('admin.academic_years.index')->with('success', 'تم الحذف بنجاح');
    }


}
