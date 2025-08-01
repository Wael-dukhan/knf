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
            $table->decimal('oral_mark', 5, 2)->nullable();
            $table->decimal('homework_mark', 5, 2)->nullable();
            $table->decimal('study_mark', 5, 2)->nullable();
            $table->decimal('work_total', 5, 2)->nullable();
            $table->decimal('exam_mark', 5, 2)->nullable();
            $table->decimal('term_total', 5, 2)->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'material_id', 'term_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('marks');
    }
}
