<?php

namespace App\Http\Controllers;

use App\Models\QuranStudentAttendanceRecord;
use App\Models\QuranClass;
use App\Models\User;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuranStudentAttendanceController extends Controller
{
    /**
     * عرض نموذج الحضور لطلاب الحلقات في مدرسة معينة
     */
    public function index(Request $request, $quranClassId)
    {
        $dateString = $request->input('date', date('Y-m-d'));
        $today = time();

        $quranClass = QuranClass::findOrFail($quranClassId);
        $schoolId = $quranClass->quranLevel->school_id;

        $students = $quranClass->students;

        $currentTerm = Term::where('school_id', $schoolId)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereNull('deleted_at') // تأكد من عدم حذف الفصل الدراسي
            ->first();

        if (!$currentTerm) {
            return redirect()->route('admin.terms.create')
                ->with('error', 'لا يوجد فصل دراسي نشط حالياً.');
        }

        $attendanceRecords = QuranStudentAttendanceRecord::whereIn('student_id', $students->pluck('id'))
            ->where('quran_class_id', $quranClassId)
            ->where('date', $dateString)
            ->get()
            ->keyBy('student_id');

        $carbonDate = Carbon::parse($dateString);
        $dayName = $carbonDate->translatedFormat('l');

        return view('attendance.quran_students', compact(
            'quranClass',
            'students',
            'currentTerm',
            'attendanceRecords',
            'dayName',
            'dateString'
        ));
    }

    /**
     * حفظ أو تحديث الحضور (AJAX)
     */
    public function ajaxUpdate(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'quran_class_id' => 'required|exists:quran_classes,id',
            'school_id' => 'required|exists:schools,id',
            'term_id' => 'required|exists:terms,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            QuranStudentAttendanceRecord::updateOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'quran_class_id' => $validated['quran_class_id'],
                    'term_id' => $validated['term_id'],
                    'date' => $validated['date'],
                ],
                [
                    'school_id' => $validated['school_id'],
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return response()->json(['message' => 'تم حفظ حضور الطالب بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()], 500);
        }
    }
}
