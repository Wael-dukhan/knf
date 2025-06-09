<?php

// database/migrations/xxxx_xx_xx_create_quran_student_attendance_records.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quran_student_attendance_records', function (Blueprint $table) {
            $table->id();

            // الطالب
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');

            // الحلقة التي ينتمي إليها الطالب
            $table->foreignId('quran_class_id')->constrained('quran_classes')->onDelete('cascade');

            // المدرسة التابعة للحلقة
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');

            // الفترة الدراسية
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');

            // التاريخ
            $table->date('date');

            // حالة الحضور
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');

            // ملاحظات إضافية
            $table->text('notes')->nullable();

            $table->timestamps();

            // لمنع تكرار السجل
            $table->unique(['student_id', 'date', 'term_id'], 'unique_student_class_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quran_student_attendance_records');
    }
};
