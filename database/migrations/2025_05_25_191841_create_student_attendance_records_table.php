<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_attendance_records', function (Blueprint $table) {
            $table->id();

            // الطالب (من جدول users)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // الشعبة الدراسية
            $table->foreignId('class_section_id')
                  ->constrained()
                  ->onDelete('cascade');

            // الفصل الدراسي
            $table->foreignId('term_id')
                  ->constrained()
                  ->onDelete('cascade');

            // التاريخ
            $table->date('date');

            // حالة الحضور
            $table->enum('status', ['present', 'absent', 'late', 'excused'])
                  ->default('present');

            // ملاحظات اختيارية
            $table->text('notes')->nullable();

            // المسجل (معلم أو مشرف)
            $table->foreignId('recorded_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance_records');
    }
};
