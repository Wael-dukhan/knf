<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuranStudentAttendanceRecord;
use App\Models\QuranClass;
use App\Models\User;
use App\Models\Term;
use App\Models\School;
use Carbon\Carbon;

class AutoMarkQuranStudentAttendance extends Command
{
    protected $signature = 'attendance:mark-default-present-for-quran-students';

    protected $description = 'Mark present for Quran students who have no attendance record for today.';

    public function handle(): int
    {
        $today = Carbon::today();

        $schools = School::all();

        foreach ($schools as $school) {

            // الحصول على الفصل الدراسي الحالي لكل مدرسة
            $termId = Term::currentTermId($school->id);

            if (! $termId) {
                $this->error("No current term found for school ID: {$school->id}");
                continue;
            }

            // جلب كل حلقات القرآن التابعة للمدرسة
            $quranClasses = QuranClass::whereHas('quranLevel', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })->get();

            $this->info("School ID: {$school->id} - Quran classes count: " . $quranClasses->count());

            foreach ($quranClasses as $quranClass) {

                // جلب طلاب الحلقة
                $students = $quranClass->students;

                foreach ($students as $student) {
                    // هل تم تسجيل حضور الطالب اليوم في هذه الحلقة؟
                    $alreadyMarked = QuranStudentAttendanceRecord::where('student_id', $student->id)
                        ->where('quran_class_id', $quranClass->id)
                        ->whereDate('date', $today)
                        ->exists();

                    $this->info("Student ID: {$student->id} - Already marked today? " . ($alreadyMarked ? 'Yes' : 'No'));

                    if (! $alreadyMarked) {
                        QuranStudentAttendanceRecord::create([
                            'student_id' => $student->id,
                            'quran_class_id' => $quranClass->id,
                            'school_id' => $school->id,
                            'term_id' => $termId,
                            'date' => $today,
                            'status' => 'present',
                        ]);
                    }
                }
            }
        }

        $this->info('Attendance marked for all Quran students without record.');
        return Command::SUCCESS;
    }
}
