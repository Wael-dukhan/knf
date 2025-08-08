<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'material_id',
        'term_id',
        'class_section_id',
        'academic_year_id',

        'oral_mark',
        'homework_mark',
        'first_study_mark',
        'second_study_mark',
        'work_total',

        'oral_exam_mark',
        'written_exam_mark',
        'term_total',
    ];
    
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    // علاقة المارك بالمادة
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function term()
    {
        return $this->belongsTo(\App\Models\Term::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(academicYear::class, 'academic_year_id');
    }

}
