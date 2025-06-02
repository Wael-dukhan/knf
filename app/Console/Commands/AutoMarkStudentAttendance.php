<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentAttendanceRecord;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoMarkStudentAttendance extends Command
{
    protected $signature = 'attendance:mark-default-present-for-students';

    protected $description = 'Mark present for students who are enrolled in class sections but have no attendance record for today.';

    public function handle(): int
    {
        $today = Carbon::today();

        // جلب الطلاب المرتبطين بشُعب دراسية مع بيانات pivot
        $studentClassSectionRows = DB::table('student_class_section')
            ->join('users', 'users.id', '=', 'student_class_section.user_id')
            ->whereNull('student_class_section.deleted_at')
            ->select('student_class_section.*', 'users.school_id')
            ->get();

        foreach ($studentClassSectionRows as $row) {
            $alreadyMarked = StudentAttendanceRecord::where('user_id', $row->user_id)
                ->whereDate('date', $today)
                ->exists();

            if (! $alreadyMarked) {
                $termId = Term::currentTermId($row->school_id);

                if (! $termId) {
                    $this->error("No current term found for student ID: {$row->user_id}");
                    continue;
                }

                StudentAttendanceRecord::create([
                    'user_id' => $row->user_id,
                    'class_section_id' => $row->class_section_id,
                    'term_id' => $termId,
                    'date' => $today,
                    'status' => 'present',
                    'recorded_by' => null,
                ]);
            }
        }

        $this->info('Attendance marked for enrolled students without record.');
        return Command::SUCCESS;
    }
}
