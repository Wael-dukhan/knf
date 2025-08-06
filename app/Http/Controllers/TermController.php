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
            'school_id' => 'required|exists:schools,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $academicYear = AcademicYear::find($request->academic_year_id);
        if (!$academicYear) {
            return back()->withErrors(['academic_year_id' => __('السنة الدراسية المختارة غير موجودة')])
                        ->withInput();
        }

        $yearStart = strtotime($academicYear->start_date);
        $yearEnd = strtotime($academicYear->end_date);

        $termStart = strtotime($request->start_date);
        $termEnd = strtotime($request->end_date);

        if ($termStart < $yearStart) {
            return back()->withErrors(['start_date' => __('تاريخ بداية الفصل يجب ألا يكون قبل بداية السنة الدراسية')])
                        ->withInput();
        }

        if ($termEnd > $yearEnd) {
            return back()->withErrors(['end_date' => __('تاريخ نهاية الفصل يجب ألا يكون بعد نهاية السنة الدراسية')])
                        ->withInput();
        }

        // تحقق عدم تداخل
        $overlappingTerm = Term::where('school_id', $request->school_id)
            ->where(function($query) use ($termStart, $termEnd) {
                $query->whereBetween('start_date', [$termStart, $termEnd])
                    ->orWhereBetween('end_date', [$termStart, $termEnd])
                    ->orWhere(function($q) use ($termStart, $termEnd) {
                        $q->where('start_date', '<=', $termStart)
                            ->where('end_date', '>=', $termEnd);
                    });
            })
            ->first();

        if ($overlappingTerm) {
            return back()->withErrors(['start_date' => __('تداخل مع فصل دراسي آخر في نفس المدرسة خلال نفس الفترة')])
                        ->withInput();
        }

        $existingTerm = Term::where('name', $request->name)
                            ->where('school_id', $request->school_id)
                            ->where('academic_year_id', $request->academic_year_id)
                            ->first();


        if ($existingTerm) {
            return back()->withErrors(['name' => __('يوجد فصل دراسي بنفس الاسم في نفس المدرسة ونفس السنة الدراسية بالفعل.')])
                        ->withInput();
        }

        $term = Term::create([
            'name' => $request->name,
            'academic_year_id' => $request->academic_year_id,
            'school_id' => $request->school_id,
            'start_date' => $termStart,
            'end_date' => $termEnd,
        ]);

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
        // التحقق من صحة البيانات الأساسية
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'school_id' => 'required|exists:schools,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // جلب السنة الدراسية المرتبطة
        $academicYear = AcademicYear::find($validated['academic_year_id']);
        if (!$academicYear) {
            return back()->withErrors(['academic_year_id' => 'السنة الدراسية المختارة غير موجودة'])->withInput();
        }

        // تحويل تواريخ السنة الدراسية إلى timestamps للمقارنة
        $yearStart = strtotime($academicYear->start_date);
        $yearEnd = strtotime($academicYear->end_date);

        // تحويل تواريخ البداية والنهاية للفصل الدراسي إلى timestamps
        $termStart = strtotime($validated['start_date']);
        $termEnd = strtotime($validated['end_date']);

        // التحقق من أن تواريخ الفصل ضمن نطاق السنة الدراسية
        if ($termStart < $yearStart || $termEnd > $yearEnd) {
            return back()->withErrors(['start_date' => 'يجب أن تكون تواريخ البداية والنهاية ضمن نطاق السنة الدراسية المختارة'])
                        ->withInput();
        }

        // التحقق من عدم وجود تكرار لنفس الفصل الدراسي في نفس المدرسة
        $existingTerm = Term::where('id', '!=', $term->id)
                            ->where('name', $request->name)
                            ->where('school_id', $request->school_id)
                            ->first();

        if ($existingTerm) {
            return back()->withErrors(['school_id' => 'هذا الفصل الدراسي مرتبط بهذه المدرسة بالفعل.'])
                        ->withInput();
        }

        // تحديث بيانات الفصل الدراسي (بعد تحويل التواريخ إلى timestamps)
        $validated['start_date'] = $termStart;
        $validated['end_date'] = $termEnd;

        $term->update($validated);

        return redirect()->route('admin.terms.index')->with('success', 'تم تحديث الفصل بنجاح');
    }



    public function destroy(Term $term)
    {
        $term->delete();
        return redirect()->route('admin.terms.index')->with('success', 'تم حذف الفصل بنجاح');
    }

    public function getTermsByYear(Request $request, $yearId)
    {
        $schoolId = $request->query('school_id'); // نمرره عبر query string

        if (!$schoolId) {
            return response()->json(['error' => 'school_id is required'], 422);
        }

        $terms = Term::where('academic_year_id', $yearId)
                    ->where('school_id', $schoolId)
                    ->orderBy('start_date')
                    ->get(['id', 'name']);

        return response()->json($terms);
    }

}
