<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mark;
use App\Models\FinalMark;
use Illuminate\Support\Facades\DB;
class CalculateFinalMarks extends Command
{
    protected $signature = 'calculate:final-marks';
    protected $description = 'حساب وتخزين محصلة علامات الطلاب النهائية لكل مادة مع استخدام العلاقات';

    public function handle()
    {
        $this->info('بدء حساب المحصلات النهائية...');

       
        // $finalMarks = DB::select("
        //     SELECT 
        //         m.student_id,
        //         m.material_id,
        //         m.class_section_id,
        //         cs.grade_id,
        //         ay.school_id,
        //         m.academic_year_id,
        //         ay.name AS academic_year_text,
        //         SUM(m.term_total) AS total_term_sum,
        //         COUNT(*) AS term_count,
        //         AVG(m.term_total) AS average_term_total
        //     FROM marks m
        //     JOIN academic_years ay ON m.academic_year_id = ay.id
        //     JOIN class_sections cs ON m.class_section_id = cs.id
        //     WHERE m.term_total IS NOT NULL
        //     GROUP BY 
        //         m.student_id,
        //         m.material_id,
        //         m.class_section_id,
        //         m.academic_year_id,
        //         cs.grade_id,
        //         ay.school_id,
        //         ay.name
        // ");
        //     // dd($finalMarks);

        // // الآن نقوم بإدخال أو تحديث البيانات في جدول final_marks
        // foreach ($finalMarks as $record) {
        //     \App\Models\FinalMark::updateOrCreate(
        //         [
        //             'student_id' => $record->student_id,
        //             'material_id' => $record->material_id,
        //             'class_section_id' => $record->class_section_id,
        //             'academic_year_id' => $record->academic_year_id,
        //             'school_id' => $record->school_id,
        //             'grade_id' => $record->grade_id,
        //         ],
        //         [
        //             'final_total' => $record->average_term_total, // المحصلة النهائية
        //         ]
        //     );
        // }



        $finalMarks = DB::table('marks as m')
            ->join('academic_years as ay', 'm.academic_year_id', '=', 'ay.id')
            ->join('class_sections as cs', 'm.class_section_id', '=', 'cs.id')
            ->join('grades as g', 'cs.grade_id', '=', 'g.id')
            ->whereNotNull('m.term_total')
            ->select(
                'm.student_id',
                'm.material_id',
                'm.class_section_id',
                'm.academic_year_id',
                'cs.grade_id',
                'g.school_id',
                DB::raw('AVG(m.term_total) as final_total'),
                DB::raw('SUM(m.term_total) as total_term_sum'),
                DB::raw('COUNT(*) as term_count')
            )
            ->groupBy(
                'm.student_id',
                'm.material_id',
                'm.class_section_id',
                'm.academic_year_id',
                'cs.grade_id',
                'g.school_id'
            )
            ->get();

        // تحديث أو إدخال السجلات في جدول final_marks
        foreach ($finalMarks as $record) {
            FinalMark::updateOrCreate(
                [
                    'student_id' => $record->student_id,
                    'material_id' => $record->material_id,
                    'class_section_id' => $record->class_section_id,
                    'academic_year_id' => $record->academic_year_id,
                    'grade_id' => $record->grade_id,
                    'school_id' => $record->school_id,
                ],
                [
                    'final_total' => round($record->final_total, 2),
                ]
            );
        }


        $this->info('تم الانتهاء من حساب المحصلات النهائية.');
    }

}
