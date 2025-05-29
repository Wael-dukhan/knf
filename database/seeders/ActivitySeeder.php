<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\ClassSection;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // بيانات الأنشطة
        $activities = [
            [
                'name' => 'تحفيظ القرآن',
                'description' => 'حلقة تحفيظ القرآن الكريم للطلاب.',
                'type' => 'قرآن',
                'class_section_id' => 1,  // تحديد الشعبة الدراسية
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'الأنشطة الترفيهية',
                'description' => 'أنشطة ترفيهية للطلاب تشمل الرياضة والفنون.',
                'type' => 'ترفيهية',
                'class_section_id' => 1,  // تحديد الشعبة الدراسية
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'الأنشطة التعليمية',
                'description' => 'أنشطة تهدف إلى تعزيز المعلومات الدراسية.',
                'type' => 'تعليمية',
                'class_section_id' => 1,  // تحديد الشعبة الدراسية
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'الأنشطة التربوية',
                'description' => 'أنشطة تهدف لتطوير مهارات الطلاب الشخصية.',
                'type' => 'تربوية',
                'class_section_id' => 1,  // تحديد الشعبة الدراسية
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        // إضافة البيانات
        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
