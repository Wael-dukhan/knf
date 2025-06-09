<?php

namespace App\Http\Controllers;

use App\Models\QuranTeacherAttendanceRecord;
use App\Models\Term;
use App\Models\User;
use App\Models\QuranClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\QuranLevel;

class QuranTeacherAttendanceController extends Controller
{
    /**
     * عرض نموذج الحضور لمعلمين حلقات قرآنية في مدرسة معينة.
     */
    public function index(Request $request, $schoolId)
    {
        $dateString = $request->input('date', date('Y-m-d'));
        // $today = strtotime($dateString);
        $today = time();

        // المعلمين الذين لديهم دور 'quran_teacher' في المدرسة
        $teachers = User::role('quran_teacher')
            ->where('school_id', $schoolId)
            ->get();

        // الحصول على الفصل الدراسي الحالي
        $currentTerm = Term::where('school_id', $schoolId)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        // إذا لم يوجد فصل دراسي نشط
        if (!$currentTerm) {
            return redirect()->route('admin.terms.create') // تأكد من وجود هذا المسار لإنشاء فصل دراسي
                            ->with('error', 'لا يوجد فصل دراسي نشط في هذا التاريخ. الرجاء إنشاء فصل دراسي أولاً.');
        }

        // سجلات الحضور حسب التاريخ والمدرسة
        $attendanceRecords = QuranTeacherAttendanceRecord::whereIn('teacher_id', $teachers->pluck('id'))
            ->where('date', $dateString)
            ->get()
            ->keyBy('teacher_id');

        $carbonDate = Carbon::parse($dateString);
        $dayName = $carbonDate->translatedFormat('l');

        return view('attendance.quran_teachers', compact(
            'schoolId',
            'teachers',
            'currentTerm',
            'attendanceRecords',
            'dayName',
            'dateString'
        ));
    }


    /**
     * تحديث الحضور لمعلمين الحلقات القرآنية (AJAX)
     */
    public function ajaxUpdate(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'date' => 'required|date',
            'school_id' => 'required|exists:schools,id',
            'term_id' => 'required|exists:terms,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // تحديث أو إنشاء سجل الحضور
            $attendance = QuranTeacherAttendanceRecord::updateOrCreate(
                [
                    'teacher_id' => $validated['teacher_id'],
                    'date' => $validated['date'],
                    'term_id' => $validated['term_id'],
                    'school_id' => $validated['school_id'],
                ],
                [
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return response()->json(['message' => 'تم حفظ حضور معلم الحلقة القرآنية بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }

}
