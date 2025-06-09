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
        Schema::create('quran_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الحلقة القرآنية
            $table->foreignId('quran_level_id')->constrained()->onDelete('cascade'); // ترتبط بمستوى قرآني
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            // وصف الحلقة القرآنية
            $table->text('description')->nullable(); // وصف اختياري للحلقة
            $table->softDeletes(); // حذف منطقي
            $table->timestamps();  // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_classes');
    }
};
