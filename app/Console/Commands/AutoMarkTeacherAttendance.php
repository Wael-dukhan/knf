<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeacherAttendanceRecord;
use App\Models\Term;
use App\Models\User;
use App\Models\School;
use Carbon\Carbon;

class AutoMarkTeacherAttendance extends Command
{ 
    protected $signature = 'attendance:mark-default-present-for-teachers';

    protected $description = 'Mark present for students who are enrolled in class sections but have no attendance record for today.';

    
    public function handle(): int
    {
        $today = Carbon::today();

        $schools = School::all();

        foreach ($schools as $school) {
            $teachers = User::role('teacher')
                            ->where('school_id', $school->id)
                            ->get();

            foreach ($teachers as $teacher) {
                $alreadyMarked = TeacherAttendanceRecord::where('teacher_id', $teacher->id)
                    ->whereDate('date', $today)
                    ->exists();

                if (! $alreadyMarked) {
                    $termId = Term::currentTermId($school->id);

                    if (! $termId) {
                        $this->error("No current term found for school ID: {$school->id}");
                        continue;
                    }

                    TeacherAttendanceRecord::create([
                        'teacher_id' => $teacher->id,
                        'school_id' => $school->id,
                        'term_id' => $termId,
                        'date' => $today,
                        'status' => 'present',
                    ]);
                }
            }
        }

        $this->info('Attendance marked for all teachers without record.');
        return Command::SUCCESS;
    }
}
