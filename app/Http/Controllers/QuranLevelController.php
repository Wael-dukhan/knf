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
        $quranLevels = QuranLevel::with(['school', 'academicYear'])->get();
        return view('quran-levels.index', compact('quranLevels'));
    }

    public function create()
    {
        $schools = School::all();
        $academicYears = AcademicYear::all();
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
        $schools = School::all();
        $academicYears = AcademicYear::all();
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
        $quranLevel->load(['quranClasses.quranTeacher', 'academicYear']);
        // dd($quranLevel);
        // حساب عدد الطلاب في كل حلقة داخل هذا المستوى
        foreach ($quranLevel->quranClasses as $class) {
            $class->loadCount(['students as student_count']);
        }

        $totalStudents = $quranLevel->quranClasses->sum('student_count');

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
}
