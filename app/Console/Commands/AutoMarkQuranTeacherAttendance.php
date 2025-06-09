<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuranTeacherAttendanceRecord;
use App\Models\Term;
use App\Models\User;
use App\Models\School;
use Carbon\Carbon;

class AutoMarkQuranTeacherAttendance extends Command
{
    protected $signature = 'attendance:mark-default-present-for-quran-teachers';

    protected $description = 'Mark present for Quran teachers without attendance record for today.';

    public function handle(): int
    {
        $today = Carbon::today();

        $schools = School::all();

        foreach ($schools as $school) {
            $quranTeachers = User::role('quran_teacher')
                            ->where('school_id', $school->id)
                            ->get();
            $this->info("School ID: {$school->id} - Quran teachers count: " . $quranTeachers->count());

            foreach ($quranTeachers as $teacher) {
                $alreadyMarked = QuranTeacherAttendanceRecord::where('teacher_id', $teacher->id)
                    ->whereDate('date', $today)
                    ->exists();
                $this->info("Teacher ID: {$teacher->id} - Already marked today? " . ($alreadyMarked ? 'Yes' : 'No'));
                if (! $alreadyMarked) {
                    $termId = Term::currentTermId($school->id);

                    if (! $termId) {
                        $this->error("No current term found for school ID: {$school->id}");
                        continue;
                    }

                    QuranTeacherAttendanceRecord::create([
                        'teacher_id' => $teacher->id,
                        'school_id' => $school->id,
                        'term_id' => $termId,
                        'date' => $today,
                        'status' => 'present',
                    ]);
                }
            }
        }

        $this->info('Attendance marked for all Quran teachers without record.');
        return Command::SUCCESS;
    }
}
