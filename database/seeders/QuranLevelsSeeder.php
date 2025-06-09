<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuranLevel;
use App\Models\School;

class QuranLevelsSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::with('academicYears')->first();

        if (!$school) {
            echo "❌ لا توجد مدرسة مسجلة. يرجى إضافة مدرسة أولاً.\n";
            return;
        }
        // dd($school);
        $academicYearId = $school->academicYears->first()->id ?? null;
        if (!$academicYearId) {
            echo "❌ المدرسة لا تحتوي على سنة دراسية. يرجى إنشاء سنة دراسية أولاً.\n";
            return;
        }

        $levels = [
            [
                'name' => 'تمهيدي',
                'description' => 'المستوى التمهيدي لتعليم الحروف ومخارجها.',
                'level_order' => 0,
            ],
            [
                'name' => 'المستوى الأول',
                'description' => 'حفظ جزء عمّ مع التلاوة والتجويد.',
                'level_order' => 1,
            ],
            [
                'name' => 'المستوى الثاني',
                'description' => 'حفظ جزء تبارك مع إتقان أحكام التجويد.',
                'level_order' => 2,
            ],
            [
                'name' => 'المستوى الثالث',
                'description' => 'حفظ 5 أجزاء ومراجعتها.',
                'level_order' => 3,
            ],
            [
                'name' => 'المستوى الرابع',
                'description' => 'حفظ 10 أجزاء مع تحسين الأداء الصوتي.',
                'level_order' => 4,
            ],
            [
                'name' => 'المستوى الخامس',
                'description' => 'إتمام حفظ نصف القرآن.',
                'level_order' => 5,
            ],
            [
                'name' => 'المستوى السادس',
                'description' => 'إتمام حفظ القرآن كاملاً.',
                'level_order' => 6,
            ],
        ];

        foreach ($levels as $level) {
            QuranLevel::firstOrCreate(
                [
                    'name' => $level['name'],
                    'level_order' => $level['level_order'],
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'description' => $level['description'],
                ]
            );
        }

        echo "✅ تم إنشاء المستويات القرآنية بنجاح.\n";
    }
}
