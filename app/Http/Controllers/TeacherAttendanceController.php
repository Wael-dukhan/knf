<?php

namespace App\Http\Controllers;

use App\Models\TeacherAttendanceRecord;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    /**
     * عرض نموذج الحضور للمعلمين في مدرسة معينة
     */
    public function index(Request $request, $schoolId)
    {
        $dateString = $request->input('date', date('Y-m-d'));
        $today = strtotime($dateString);

        // المعلمين النشطين في المدرسة
        $teachers = User::whereHas('roles', function ($query) {
                $query->where('name', 'teacher');
            })
            ->where('school_id', $schoolId)
            ->get();

        // الفصل الدراسي الحالي
        $currentTerm = Term::where('school_id', $schoolId)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        // سجلات الحضور حسب التاريخ والمدرسة
        $attendanceRecords = TeacherAttendanceRecord::whereIn('teacher_id', $teachers->pluck('id'))
            ->where('date', $dateString)
            ->get()
            ->keyBy('teacher_id');

        $carbonDate = Carbon::parse($dateString);
        $dayName = $carbonDate->translatedFormat('l');

        return view('attendance.teacher_school', compact(
            'schoolId',
            'teachers',
            'currentTerm',
            'attendanceRecords',
            'dayName',
            'dateString'
        ));
    }

    /**
     * تحديث الحضور للمعلمين (AJAX)
     */
    public function ajaxUpdate(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'date' => 'required|date',
            'school_id' => 'required|exists:schools,id',
            'term_id' => 'required|exists:terms,id',
            'notes' => 'nullable|string|max:1000',
        ]);
        // dd($request);

        try {
            $attendance = TeacherAttendanceRecord::updateOrCreate(
                [
                    'teacher_id' => $validated['teacher_id'],
                    'date' => $validated['date'],
                    'term_id' => $validated['term_id'],
                    'school_id' => $validated['school_id'], // تأكد أن هذا العمود موجود بقاعدة البيانات
                ],
                [
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'class_section_id' => null, // اختياري لأن الحضور على مستوى المدرسة
                ]
            );

            return response()->json(['message' => 'تم حفظ حضور المعلم بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
