<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StudentYearlyTotalMarksCommand extends Command
{
    /**
     * اسم الأمر الذي سيتم تنفيذه من التيرمنال.
     *
     * @var string
     */
    protected $signature = 'student-yearly-total-marks';

    /**
     * وصف الأمر.
     *
     * @var string
     */
    protected $description = 'Calculate yearly total and average marks for each student';

    /**
     * تنفيذ الأمر.
     */
    public function handle()
    {
        $this->info('Calculating yearly total marks...');

        // تنفيذ الاستعلام
        $yearlyTotals = DB::table('final_marks')
            ->select(
                'student_id',
                'school_id',
                'grade_id',
                'class_section_id',
                'academic_year_id',
                DB::raw('SUM(final_total) as total_score'),
                DB::raw('COUNT(DISTINCT material_id) as material_count'),
                DB::raw('ROUND(SUM(final_total)/COUNT(DISTINCT material_id)) as average_score')
            )
            ->groupBy(
                'student_id',
                'school_id',
                'grade_id',
                'class_section_id',
                'academic_year_id'
            )
            ->get();

        // إدخال النتائج في جدول student_yearly_total_marks
        foreach ($yearlyTotals as $row) {
            DB::table('student_yearly_total_marks')->updateOrInsert(
                [
                    'student_id' => $row->student_id,
                    'school_id' => $row->school_id,
                    'grade_id' => $row->grade_id,
                    'class_section_id' => $row->class_section_id,
                    'academic_year_id' => $row->academic_year_id,
                ],
                [
                    'total_score' => $row->total_score,
                    'material_count' => $row->material_count,
                    'average_score' => $row->average_score,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->info('Yearly total marks calculation completed.');
    }
}
