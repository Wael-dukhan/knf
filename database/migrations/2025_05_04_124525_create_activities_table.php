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
        Schema::create('activities', function (Blueprint $table) {
            $table->id(); // العمود الأساسي (ID)
            $table->string('name'); // اسم النشاط
            $table->text('description')->nullable(); // وصف النشاط
            $table->enum('type', ['قرآن', 'ترفيهية', 'تعليمية', 'تربوية']); // نوع النشاط
            $table->foreignId('class_section_id')->constrained()->onDelete('cascade'); // ربط النشاط بالشعبة الدراسية
            $table->timestamps(); // الوقت الذي تم فيه إنشاء وتحديث النشاط
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities'); // حذف الجدول في حالة التراجع عن الميجرات
    }
};
