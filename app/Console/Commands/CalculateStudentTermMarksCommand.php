<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ClassSection;
use App\Models\StudentTermTotalMark;

class CalculateStudentTermMarksCommand extends Command
{
    protected $signature = 'marks:calculate-term-totals';

    protected $description = 'Calculate and store average term marks for each student';

     public function handle()
    {
        $this->info('🔄 جاري حساب مجموع علامات الفصل الدراسي لكل طالب...');

        // استعلام للحصول على مجموع العلامات حسب الطالب والفصل الدراسي
        $marks = DB::table('marks')
            ->select(
                'student_id',
                'class_section_id',
                'academic_year_id',
                'term_id',
                DB::raw('SUM(term_total) as total_score'),
                DB::raw('COUNT(DISTINCT material_id) as material_count'),
                DB::raw('CEIL(SUM(term_total) / COUNT(DISTINCT material_id)) as average_score')
            )
            ->groupBy(
                'student_id',
                'class_section_id',
                'academic_year_id',
                'term_id'
            )
            ->get();

        foreach ($marks as $mark) {
            $classSection = ClassSection::find($mark->class_section_id);

            if (!$classSection) {
                $this->warn("⚠️ لم يتم العثور على الشعبة ID: {$mark->class_section_id}");
                continue;
            }

            StudentTermTotalMark::updateOrCreate(
                [
                    'student_id' => $mark->student_id,
                    'school_id' => $classSection->grade->school_id,
                    'grade_id' => $classSection->grade_id,
                    'class_section_id' => $mark->class_section_id,
                    'academic_year_id' => $mark->academic_year_id,
                    'term_id' => $mark->term_id,
                ],
                [
                    'total_score' => $mark->total_score,
                    'average_score' => $mark->average_score,
                    'material_count' => $mark->material_count,
                ]
            );
        }

        $this->info('✅ تم حساب وتخزين مجموع علامات الفصل الدراسي بنجاح.');
    }
}
