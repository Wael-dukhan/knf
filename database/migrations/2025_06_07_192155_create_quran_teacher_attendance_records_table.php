<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quran_teacher_attendance_records', function (Blueprint $table) {
            $table->id();

            // المعلم (معلم حلقة)
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');

            // المدرسة التي تتبع لها الحلقة
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');

            // الفترة الدراسية (term)
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');

            // تاريخ الحضور
            $table->date('date');

            // حالة الحضور
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');

            // ملاحظات إضافية
            $table->text('notes')->nullable();

            $table->timestamps();

            // لضمان عدم وجود تسجيل مكرر في نفس اليوم لنفس المعلم ونفس الحلقة والفترة
            $table->unique(['teacher_id', 'date', 'term_id'], 'unique_teacher_class_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quran_teacher_attendance_records');
    }
};
