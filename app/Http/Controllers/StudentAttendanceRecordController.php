<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendanceRecord;
use App\Models\ClassSection;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAttendanceRecordController extends Controller
{
    /**
     * عرض نموذج الحضور لشعبة دراسية معينة
     */
    public function index(Request $request, ClassSection $classSection)
    {

        

        // استخدام التاريخ المُمرر أو اليوم الحالي
        $dateString = $request->input('date', date('Y-m-d'));
        $today = strtotime($dateString); // تحويله لـ timestamp

        // الطلاب النشطين في الشعبة
        $students = $classSection->users()
            ->wherePivot('status', 'active')
            ->get();

        // الفصل الدراسي الحالي بناءً على التاريخ
        $currentTerm = Term::where('academic_year_id', $classSection->grade->academic_year_id)
            ->where('school_id', $classSection->grade->school_id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        // سجلات الحضور لليوم المحدد
        $attendanceRecords = StudentAttendanceRecord::where('class_section_id', $classSection->id)
            ->where('date', $dateString)
            ->get()
            ->keyBy('user_id');

        $carbonDate = Carbon::parse($dateString);
        $dayName = $carbonDate->translatedFormat('l'); // اسم اليوم مترجم حسب اللغة الحالية


        return view('attendance.student_class_section', compact(
            'classSection',
            'students',
            'currentTerm',
            'attendanceRecords',
            'dayName',
            'dateString' // نمرر التاريخ للعرض
        ));
    }




    public function ajaxUpdate(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'date' => 'required|date',
            'class_section_id' => 'required|exists:class_sections,id',
            'term_id' => 'required|exists:terms,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $attendance = StudentAttendanceRecord::updateOrCreate(
                [
                    'user_id' => $validated['student_id'],
                    'class_section_id' => $validated['class_section_id'],
                    'date' => $validated['date'],
                    'term_id' => $validated['term_id'],
                ],
                [
                    'status' => $validated['status'],
                    'notes' => $validated['notes'] ?? null,
                    'recorded_by' => Auth::id(), // إذا كان موجود في جدول الحضور
                ]
            );

            return response()->json(['message' => 'تم حفظ الحالة بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء الحفظ: ' . $e->getMessage()], 500);
        }
    }


}
