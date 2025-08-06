<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\ClassSection;
use App\Models\Mark;

class MarkEntryController extends Controller
{

    // public function create($materialId, $sectionId)
    // {
    //     // جلب المادة المطلوبة
    //     $material = Material::findOrFail($materialId);

    //     // جلب الشعبة المطلوبة
    //     $classSection = ClassSection::findOrFail($sectionId);

    //     // جلب الطلاب النشيطين المرتبطين بالشعبة
    //     $students = $classSection->users;

    //     // جلب معلومات إضافية عن الشعبة (الصف، السنة الدراسية، المدرسة)
    //     $classSectionInfo = \Illuminate\Support\Facades\DB::table('class_sections')
    //         ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
    //         ->join('academic_years', 'grades.academic_year_id', '=', 'academic_years.id')
    //         ->join('schools', 'grades.school_id', '=', 'schools.id')
    //         ->select(
    //             'class_sections.*',
    //             'grades.name as grade_name',
    //             'academic_years.name as academic_year_name',
    //             'schools.id as school_id',
    //             'schools.name as school_name',
    //         )
    //         ->where('class_sections.id', $sectionId)
    //         ->first();
    //     $termName = \App\Models\Term::currentTermName($classSectionInfo->school_id);
        
    //     // تمرير البيانات إلى الـ view
    //     return view('marks.create', compact('material', 'students', 'classSectionInfo','termName'));
    // }


public function create(Request $request, $materialId, $sectionId)
{
    // جلب المادة المطلوبة
    $material = Material::findOrFail($materialId);

    // جلب الشعبة المطلوبة
    $classSection = ClassSection::findOrFail($sectionId);

    // جلب الطلاب المرتبطين بالشعبة
    $students = $classSection->users;

    // جلب معلومات إضافية عن الشعبة (الصف، السنة الدراسية، المدرسة)
    $classSectionInfo = \Illuminate\Support\Facades\DB::table('class_sections')
        ->join('grades', 'class_sections.grade_id', '=', 'grades.id')
        ->join('academic_years', 'grades.academic_year_id', '=', 'academic_years.id')
        ->join('schools', 'grades.school_id', '=', 'schools.id')
        ->select(
            'class_sections.*',
            'grades.academic_year_id',
            'grades.name as grade_name',
            'academic_years.name as academic_year_name',
            'academic_years.status as academic_year_status',
            'schools.id as school_id',
            'schools.name as school_name'
        )
        ->where('class_sections.id', $sectionId)
        ->first();

    // حالة السنة الدراسية (نشطة أو غير نشطة)
    $academicYearActive = $classSectionInfo->academic_year_status === 'active';
    if (! $academicYearActive) {
        return back()
            ->withErrors([
                'academic_year_id' => __('messages.academic_year_not_active')
            ])
            ->withInput();
    }
    // جلب الفصول الدراسية المرتبطة بنفس السنة الدراسية فقط
    $terms = \App\Models\Term::with('academicYear')
    ->whereHas('academicYear', function ($q) use ($classSectionInfo) {
        $q->where('id', $classSectionInfo->academic_year_id)
          ->where('school_id', $classSectionInfo->school_id)
          ->where('status', 'active');
    })
    ->select('id', 'name', 'start_date', 'end_date')
    ->orderBy('start_date')
    ->get();

    // dd($terms);
    // جلب term_id من الرابط (query param) إذا موجود وصالح
    $requestedTermId = $request->query('term_id');

    if ($requestedTermId && $terms->contains('id', $requestedTermId)) {
        $termId = $requestedTermId;
        $termName = $terms->firstWhere('id', $termId)->name;
    } else {
        // جلب الفصل الدراسي الحالي بناءً على التاريخ ضمن نفس السنة الدراسية
        $currentTimestamp = time();
        $currentTerm = $terms->first(function ($term) use ($currentTimestamp) {
            return $term->start_date <= $currentTimestamp && $term->end_date >= $currentTimestamp;
        });

        if ($currentTerm) {
            $termId = $currentTerm->id;
            $termName = $currentTerm->name;
        } else {
            // لو ما فيه فصل حالي، خذ أول فصل موجود أو null
            $termId = $terms->first()?->id;
            $termName = $terms->first()?->name ?? __('messages.no_term_found');
        }
    }

    // جلب العلامات الموجودة مسبقاً لهذه المادة والشعبة والفصل المختار
    $existingMarks = Mark::where('material_id', $materialId)
        ->where('term_id', $termId)
        ->whereIn('student_id', $students->pluck('id'))
        ->get()
        ->keyBy('student_id');

    // تمرير البيانات إلى الـ view مع حالة السنة الدراسية
    return view('marks.create', compact(
        'material',
        'students',
        'classSectionInfo',
        'termId',
        'termName',
        'existingMarks',
        'terms',
        'academicYearActive'
    ));
}

    public function store(Request $request)
    {
        // التحقق من صحة الطلب
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'marks' => 'required|array',
            'marks.*.oral' => 'nullable|numeric|min:0|max:100',
            'marks.*.homework' => 'nullable|numeric|min:0|max:100',
            'marks.*.first_study' => 'nullable|numeric|min:0|max:100',
            'marks.*.second_study' => 'nullable|numeric|min:0|max:100',
            'marks.*.oral_exam' => 'nullable|numeric|min:0|max:100',
            'marks.*.written_exam' => 'nullable|numeric|min:0|max:100',
        ]);

        $material = Material::findOrFail($request->material_id);
        $schoolId = $material->grade->school_id;
        $termId = \App\Models\Term::currentTermId($schoolId);

        if (!$termId) {
            return redirect()->back()->withErrors(['term_id' => 'لا يوجد فصل دراسي حالي مطابق لتاريخ اليوم.']);
        }

        foreach ($request->marks as $studentId => $markData) {
            $oral = floatval($markData['oral'] ?? 0);
            $homework = floatval($markData['homework'] ?? 0);
            $firstStudy = floatval($markData['first_study'] ?? 0);
            $secondStudy = floatval($markData['second_study'] ?? 0);
            $oralExam = floatval($markData['oral_exam'] ?? 0);
            $writtenExam = floatval($markData['written_exam'] ?? 0);
            // مجموع الأعمال = متوسط (oral, homework, firstStudy, secondStudy)
            $workTotal = ($oral + $homework + $firstStudy + $secondStudy) / 4;

            // المجموع الكلي = متوسط (workTotal + oralExam + writtenExam)
            $termTotal = ($workTotal + $oralExam + $writtenExam) / 3;

            Mark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'material_id' => $request->material_id,
                    'term_id' => $termId,
                ],
                [
                    'oral_mark' => $oral,
                    'homework_mark' => $homework,
                    'first_study_mark' => $firstStudy,
                    'second_study_mark' => $secondStudy,
                    'oral_exam_mark' => $oralExam,
                    'written_exam_mark' => $writtenExam,
                    'work_total' => $workTotal,
                    'term_total' => $termTotal,
                ]
            );
        }

        return redirect()->back()->with('success', __('messages.marks.success_message'));
    }



}
