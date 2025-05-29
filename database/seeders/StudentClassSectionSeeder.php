<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ClassSection;
use Carbon\Carbon;  // لاستخدام الوقت الحالي

class StudentClassSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على جميع الشُعب
        $classSections = ClassSection::all();

        // التأكد من وجود طلاب في قاعدة البيانات باستخدام الحزمة spatie
        $students = User::role('student')->get(); // استخدام role() للحصول على الطلاب

        // الوقت الحالي
        $currentTime = Carbon::now();

        foreach ($students as $student) {
            // اختيار شعبة عشوائية من الشُعب المتاحة
            $randomClassSection = $classSections->random();

            // ربط الطالب بالشعبة العشوائية مع إضافة created_at و updated_at
            $student->classSections()->attach($randomClassSection->id, [
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);
        }
    }
}
