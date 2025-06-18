<?php

namespace App\Http\Controllers;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TermController extends Controller
{
    public function index()
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        // الحصول على جميع الفصول الدراسية مع السنة الدراسية والمدرسة
        // في حالة المشرف العام، يمكن عرض جميع الفصول الدراسية
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع الفصول الدراسية
            $terms = Term::with('academicYear', 'school')->get();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض الفصول الدراسية الخاصة بالمدرسة التي يديرها
            $terms = Term::with('academicYear', 'school')
                ->where('school_id', $user->school_id)
                ->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // dd($terms);
        return view('admin.terms.index', compact('terms'));
    }

    public function create()
    {
        // الحصول على قائمة السنوات الدراسية والمدارس
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role === 'super_admin') {
            // في حالة المشرف العام، يمكن عرض جميع السنوات الدراسية
            $academicYears = AcademicYear::all();
            $schools = School::withoutTrashed()->get();
        } else if ($role === 'school_manager') {
            // في حالة مدير المدرسة، يمكن عرض السنوات الدراسية الخاصة بالمدرسة التي يديرها
            $academicYears = AcademicYear::where('school_id', $user->school_id)->get();
            $schools = School::where('id', $user->school_id)->withoutTrashed()->get();
            // dd($academicYears);
        } else if ($role === 'teacher' || $role === 'quran_teacher') {
            // في حالة المعلم أو معلم القرآن، يمكن عرض المدارس التي يعملون بها
            $academicYears = AcademicYear::whereHas('schools', function ($query) use ($user) {
                $query->where('id', $user->school_id);
            })->get();
            $schools = School::whereHas('teachers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->withoutTrashed()->get();
        } else {
            // في حالة الأدوار الأخرى، يمكن إعادة توجيه المستخدم أو عرض رسالة خطأ
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        return view('admin.terms.create', compact('academicYears', 'schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'school_id' => 'required|exists:schools,id', // مدرسة واحدة فقط
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        // تحقق من أن الفصل الدراسي بنفس الاسم غير مرتبط بالفعل بنفس المدرسة
        $existingTerm = Term::where('name', $request->name)
                            ->where('school_id', $request->school_id)
                            ->first();

        if ($existingTerm) {
            return back()->withErrors(['school_id' => __('هذا الفصل الدراسي مرتبط بهذه المدرسة بالفعل.')])
                        ->withInput();
        }

        $term = Term::create([
            'name' => $request->name,
            'academic_year_id' => $request->academic_year_id,
            'school_id'=> $request->school_id,
            'start_date' => strtotime($request->start_date),
            'end_date' => strtotime($request->end_date),
        ]);

        // ربط الفصل الدراسي بالمدرسة المحددة
        // $term->schools()->attach($request->school_id);

        return redirect()->route('admin.terms.index')->with('success', __('تم إضافة الفصل الدراسي بنجاح.'));
    }


    public function edit($id)
    {
        // التحقق من صلاحية المستخدم
        $user = Auth::user(); // الحصول على المستخدم الحالي
        // الحصول على دور المستخدم
        $role = $user->getRoleNames()->first(); // افتراض أنه يوجد دور واحد فقط
        if ($role !== 'super_admin' && $role !== 'school_manager') {
            return redirect()->route('dashboard')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
        }
        // العثور على الفصل الدراسي
        $term = Term::findOrFail($id);

        // الحصول على قائمة المدارس والسنة الدراسية
        $academicYears = AcademicYear::all();
        $schools = School::withoutTrashed()->get();

        // تمرير البيانات إلى الـ View
        return view('admin.terms.edit', compact('term', 'academicYears', 'schools'));
    }


    public function update(Request $request, Term $term)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        $validated['start_date'] = strtotime($validated['start_date']);
        $validated['end_date'] = strtotime($validated['end_date']);

        // التحقق من عدم وجود تكرار لنفس الفصل الدراسي في نفس المدرسة
        $existingTerm = Term::where('id', '!=', $term->id)
                            ->where('name', $request->name)
                            ->where('school_id', $request->school_id)
                            ->first();

        if ($existingTerm) {
            return back()->withErrors(['school_id' => 'هذا الفصل الدراسي مرتبط بهذه المدرسة بالفعل.'])
                        ->withInput();
        }

        // تحديث بيانات الفصل الدراسي
        $term->update($validated);

        // تحديث المدرسة المرتبطة بالفصل الدراسي
        // $term->schools()->sync([$request->school_id]);

        return redirect()->route('admin.terms.index')->with('success', 'تم تحديث الفصل بنجاح');
    }



    public function destroy(Term $term)
    {
        $term->delete();
        return redirect()->route('admin.terms.index')->with('success', 'تم حذف الفصل بنجاح');
    }
}
