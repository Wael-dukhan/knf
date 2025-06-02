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
        Schema::create('student_class_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ربط الطالب
            $table->foreignId('class_section_id')->constrained()->onDelete('cascade'); // ربط الشعبة
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade'); // ربط السنة الدراسية
            $table->enum('status', [
                'active',
                'dropout', // متسرب
                'expelled', // مطرود
                'transferred_to_another_school',
                'temporarily_suspended' // معلق مؤقتاً
            ])->default('active');
            $table->softDeletes(); // هذا يضيف عمود deleted_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class_section');
    }
};
