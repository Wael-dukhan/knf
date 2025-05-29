<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialTeacherAssignment extends Model
{
    use HasFactory;

    protected $table = 'material_teacher_term_class_section'; // تحديد اسم الجدول

    protected $fillable = [
        'academic_year_id',
        'term_id',
        'class_section_id',
        'material_id',
        'teacher_id',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
