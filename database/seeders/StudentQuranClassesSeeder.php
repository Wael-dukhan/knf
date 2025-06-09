<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\QuranClass;
use App\Models\QuranLevel;
use Illuminate\Support\Facades\DB;

class StudentQuranClassesSeeder extends Seeder
{
    public function run(): void
    {
        $quranClasses = QuranClass::all();

        if ($quranClasses->isEmpty()) {
            echo "❌ لا توجد حلقات قرآنية.\n";
            return;
        }

        foreach ($quranClasses as $class) {
            $level = QuranLevel::find($class->quran_level_id);
            if (!$level) continue;

            // جلب الطلاب من نفس مدرسة الحلقة فقط
            $students = User::role('student')
                ->where('school_id', $level->school_id)  // تأكد من وجود عمود school_id في جدول quran_classes
                ->whereNull('deleted_at') // تأكد من عدم حذف الطالب
                ->get();

            if ($students->isEmpty()) {
                echo "⚠️ لا يوجد طلاب في مدرسة الحلقة: {$class->name}\n";
                continue;
            }

            $studentsPerClass = 5;

            // اختر طلاب عشوائيين من نفس المدرسة بدون تكرار
            $selectedStudents = $students->random(min($studentsPerClass, $students->count()));

            foreach ($selectedStudents as $student) {
                $exists = DB::table('student_quran_classes')
                    ->where('user_id', $student->id)
                    ->where('quran_level_id', $level->id)
                    ->where('status', 'active')
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('student_quran_classes')->insert([
                    'user_id' => $student->id,
                    'quran_class_id' => $class->id,
                    'quran_level_id' => $level->id,
                    'status' => 'active',
                    'joined_at' => now()->toDateString(),
                    'completed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        echo "✅ تم تعيين الطلاب في الحلقات القرآنية بنجاح.\n";
    }
}
