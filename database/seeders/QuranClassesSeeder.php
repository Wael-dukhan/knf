<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuranClass;
use App\Models\QuranLevel;
use App\Models\User;
use App\Models\School;

class QuranClassesSeeder extends Seeder
{
    public function run(): void
    {
        $levels = QuranLevel::all();

        // لا تحتاج لجلب المدارس لأن الجدول لا يحتوي على school_id
        // لكن يمكنك جلب المعلمين حسب الدور 'quran_teacher'

        
        foreach ($levels as $level) {
            $teachers = User::role('quran_teacher')->where('school_id' , $level->school_id)->get();
            for ($i = 1; $i <= 2; $i++) {
                $teacher = $teachers->random();

                QuranClass::create([
                    'name' => 'حلقة ' . $level->name . ' - ' . $i,
                    'quran_level_id' => $level->id,
                    'teacher_id' => $teacher->id,
                    'description' => 'حلقة لحفظ وتلاوة القرآن في ' . $level->name,
                ]);
            }
        }
    }
}
