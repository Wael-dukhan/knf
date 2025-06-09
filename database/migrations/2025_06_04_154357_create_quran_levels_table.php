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
        Schema::create('quran_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المستوى القرآني
            $table->unsignedTinyInteger('level_order'); // ترتيب المستوى
            $table->text('description')->nullable(); // وصف اختياري
            $table->foreignId('school_id')->constrained()->onDelete('cascade'); // المدرسة المرتبطة
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->softDeletes(); // دعم الحذف المنطقي
            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_levels');
    }
};
