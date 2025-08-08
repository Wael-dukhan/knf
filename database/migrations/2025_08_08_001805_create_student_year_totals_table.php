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
        Schema::create('student_yearly_total_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('grade_id');
            $table->unsignedBigInteger('class_section_id'); // ✅ الشعبة
            $table->unsignedBigInteger('academic_year_id');
            $table->decimal('total_score', 8, 2);
            $table->decimal('average_score', 8, 2);
            $table->unsignedInteger('material_count');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('set null');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('set null');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');

            // قيد فريد مركب لمنع التكرار
            $table->unique([
                'student_id',
                'school_id',
                'grade_id',
                'class_section_id',
                'academic_year_id',
            ], 'student_year_unique');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_year_totals');
    }
};
