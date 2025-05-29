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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الصف
            $table->enum('grade_level', [1, 2, 3]); 
            $table->text('description')->nullable(); // وصف اختياري
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');  // foreign key 
            $table->softDeletes(); // هذا يضيف عمود deleted_at

            $table->timestamps(); // created_at و updated_at
            // إضافة قيود على العمود school_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
