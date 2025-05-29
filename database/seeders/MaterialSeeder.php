<?php

// database/seeders/MaterialSeeder.php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        // الحصول على الصفوف المدرسية والـ Teachers
        $grades = Grade::all();
        
        // إضافة بيانات المواد الدراسية
        foreach ($grades as $grade) {
            $i = 1; 
            $teachers = User::role('teacher')->where('school_id', $grade->school->id)->get(); // يجلب كل المستخدمين الذين لديهم دور "teacher"
            foreach ($teachers as $teacher) {
                $material = Material::create([
                    'name' => 'مادة ' . $i,
                    'description' => 'وصف المادة الخاصة بالصف ' . $grade->name,
                    'grade_id' => $grade->id,
                ]);
                $i++;

            }
        }
    }
}
