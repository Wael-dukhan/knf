<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\ClassSection;
use App\Models\Mark;

class MarkEntryController extends Controller
{
    public function create($materialId, $sectionId)
    {
        $material = Material::findOrFail($materialId);
        $classSection = ClassSection::findOrFail($sectionId);

        // جلب الطلاب المرتبطين بالشعبة (الطلاب "النشيطين" فقط)
        $students = $classSection->users;

        return view('marks.create', compact('material', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'marks' => 'required|array',
            'marks.*.oral' => 'nullable|numeric|min:0|max:100',
            'marks.*.homework' => 'nullable|numeric|min:0|max:100',
            'marks.*.study' => 'nullable|numeric|min:0|max:100',
            'marks.*.exam' => 'nullable|numeric|min:0|max:100',
        ]);

        // تحديد المدرسة من المادة أو من المستخدم الحالي (اختر المناسب لك)
        $material = Material::findOrFail($request->material_id);
        $schoolId = $material->grade->school_id;
        // تحديد الفصل الدراسي الحالي
        $termId = \App\Models\Term::currentTermId($schoolId);
        // dd($termId);

        if (!$termId) {
            return redirect()->back()->withErrors(['term_id' => 'لا يوجد فصل دراسي حالي مطابق لتاريخ اليوم.']);
        }

        foreach ($request->marks as $studentId => $markData) {
            $oral = $markData['oral'] ?? 0;
            $homework = $markData['homework'] ?? 0;
            $study = $markData['study'] ?? 0;
            $exam = $markData['exam'] ?? 0;

            $workTotal = $oral + $homework + $study;
            $termTotal = $workTotal + $exam;

            Mark::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'material_id' => $request->material_id,
                    'term_id' => $termId,
                ],
                [
                    'oral_mark'     => $oral,
                    'homework_mark' => $homework,
                    'study_mark'    => $study,
                    'work_total'    => $workTotal,
                    'exam_mark'     => $exam,
                    'term_total'    => $termTotal,
                ]
            );
        }

        return redirect()->back()->with('success', __('messages.marks.success_message'));
    }


}
