<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\School;
use Carbon\Carbon;

class TermsSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();

        foreach ($schools as $school) {
            $academicYears = AcademicYear::where('school_id', $school->id)->get();

            foreach ($academicYears as $year) {

                // استخدم Carbon لتحديد التواريخ كنقاط فصلية
                $startOfYear = Carbon::parse($year->start_date);
                $midOfYear = $startOfYear->copy()->addMonths(4)->startOfDay();
                $endOfYear = Carbon::parse($year->end_date);

                // الفصل الأول: من بداية السنة الدراسية حتى منتصف السنة (4 أشهر)
                Term::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'academic_year_id' => $year->id,
                        'name' => "الفصل 1 للسنة {$year->name}",
                    ],
                    [
                        'start_date' => $startOfYear->timestamp,
                        'end_date' => $midOfYear->timestamp,
                    ]
                );

                // الفصل الثاني: من منتصف السنة حتى نهاية السنة الدراسية
                Term::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'academic_year_id' => $year->id,
                        'name' => "الفصل 2 للسنة {$year->name}",
                    ],
                    [
                        'start_date' => $midOfYear->addDay()->timestamp, // يبدأ اليوم التالي لنهاية الفصل الأول
                        'end_date' => $endOfYear->timestamp,
                    ]
                );
            }
        }
    }
}
