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
        Schema::create('final_marks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('grade_id');         
            $table->unsignedBigInteger('class_section_id');

            // أضف العمود academic_year_id هنا
            $table->unsignedBigInteger('academic_year_id');

            // $table->decimal('term1_total', 5, 2)->nullable();
            // $table->decimal('term2_total', 5, 2)->nullable();
            // $table->decimal('term3_total', 5, 2)->nullable();
            $table->decimal('final_total', 5, 2)->nullable();

            $table->timestamps();

            // المفاتيح الأجنبية
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('class_section_id')->references('id')->on('class_sections')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');

            $table->unique(['student_id', 'material_id', 'school_id', 'grade_id', 'class_section_id','academic_year_id'], 'uniq_final_mark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_marks');
    }
};
