<?php

namespace Database\Seeders;

use App\Models\ClassSection;
use App\Models\Grade;
use Illuminate\Database\Seeder;

class ClassSectionSeeder extends Seeder
{
    public function run(): void
    {
        $grades = Grade::all();

        foreach ($grades as $grade) {
            ClassSection::create([
                'name' => 'الشعبة أ',
                'grade_id' => $grade->id,
            ]);

            ClassSection::create([
                'name' => 'الشعبة ب',
                'grade_id' => $grade->id,
            ]);
        }
    }
}
