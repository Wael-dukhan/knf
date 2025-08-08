<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentYearlyTotalMarks extends Model
{
    protected $table = 'student_yearly_total_marks';

    protected $fillable = [
        'student_id',
        'school_id',
        'grade_id',
        'class_section_id',
        'academic_year_id',
        'total_score',
        'average_score',
        'material_count',
    ];

    // إذا حابب تضيف علاقات مثل علاقة الطالب:
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // علاقة المدرسة:
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    // علاقة الصف:
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    // علاقة الشعبة:
    public function classSection()
    {
        return $this->belongsTo(ClassSection::class, 'class_section_id');
    }

    // علاقة السنة الدراسية:
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
