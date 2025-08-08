<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarksTable extends Migration
{
    public function up()
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            $table->foreignId('class_section_id')->constrained('class_sections')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');

            $table->decimal('oral_mark', 5, 2);           // المشاركة الشفوية (إن أردت إبقاؤه)
            $table->decimal('homework_mark', 5, 2);
            $table->decimal('first_study_mark', 5, 2);    // المذاكرة الأولى
            $table->decimal('second_study_mark', 5, 2);   // المذاكرة الثانية
            $table->decimal('work_total', 5, 2);
            $table->decimal('oral_exam_mark', 5, 2);      // الامتحان الفصلي الشفهي
            $table->decimal('written_exam_mark', 5, 2);   // الامتحان الفصلي التحريري
            $table->decimal('term_total', 5, 2);

            $table->timestamps();

            $table->unique(['student_id', 'material_id', 'term_id','class_section_id','academic_year_id'],'marks_unique_combination');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marks');
    }
}
