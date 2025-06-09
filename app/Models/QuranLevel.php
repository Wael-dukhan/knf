<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuranLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'level_order',
        'description',
        'school_id',
        'academic_year_id',
    ];

    protected $casts = [
        'level_order' => 'integer',
    ];
    protected $with = ['school', 'academicYear']; // إن كنت تريد تحميل العلاقات افتراضياً

    /**
     * علاقة المستوى بالمدرسة
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * علاقة المستوى بالحلقات القرآنية
     */
    public function quranClasses()
    {
        return $this->hasMany(QuranClass::class);
    }

    /**
     * علاقة المستوى بالسنة الدراسية
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    
   
}
