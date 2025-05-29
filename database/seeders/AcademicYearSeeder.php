<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\School;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first(); // تأكد أن هناك مدرسة واحدة على الأقل في قاعدة البيانات

        AcademicYear::create([
            'name' => '2023/2024',
            'school_id' => $school->id,
        ]);

        AcademicYear::create([
            'name' => '2024/2025',
            'school_id' => $school->id,
        ]);
    }
}
