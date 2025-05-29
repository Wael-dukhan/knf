<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\School;

class TermsSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        $academicYears = AcademicYear::all();

        foreach ($schools as $school) {
            foreach ($academicYears as $year) {
                for ($i = 1; $i <= 2; $i++) {

                    Term::create([
                        'name' => "الفصل {$i} للسنة {$year->name}",
                        'academic_year_id' => $year->id,
                        'school_id'=> $school->id,
                        'start_date' => time(),
                        'end_date' => time() + 6*30*24*60*60,
                    ]);
                }
            }
        }
    }
}
