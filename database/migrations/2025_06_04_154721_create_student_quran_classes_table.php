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
        Schema::create('student_quran_classes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // الطالب
            $table->foreignId('quran_class_id')->constrained()->onDelete('cascade'); // الحلقة القرآنية
            $table->foreignId('quran_level_id')->constrained()->onDelete('cascade'); // المستوى القرآني

            // حالة الطالب في الحلقة
            $table->enum('status', [
                'active',
                'completed',
                'withdrawn', // انسحب
                'suspended', // موقوف مؤقتاً
            ])->default('active');

            // تاريخ الالتحاق وتاريخ الإكمال
            $table->date('joined_at')->default(now());
            $table->date('completed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // منع تكرار الطالب في أكثر من حلقة بنفس الوقت (في نفس المستوى)
            $table->unique(['user_id', 'quran_level_id', 'status'], 'uq_user_level_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_quran_class');
    }
};
