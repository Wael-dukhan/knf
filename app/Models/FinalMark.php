<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalMark extends Model
{
    protected $fillable = [
        'student_id',
        'material_id',
        'school_id',
        'grade_id',
        'class_section_id',
        'academic_year_id',
        'term1_total',
        'term2_total',
        'term3_total',
        'final_total',
    ];

    // علاقات (اختياري حسب الحاجة)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
