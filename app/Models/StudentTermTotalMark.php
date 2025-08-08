<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTermTotalMark extends Model
{
    use HasFactory;
    protected $table = 'student_term_total_marks'; // تأكيد اسم الجدول (اختياري إذا كان الاسم مطابقًا للقاعدة)
    protected $fillable = [
        'student_id',
        'school_id',
        'grade_id',
        'class_section_id',
        'academic_year_id',
        'term_id',
        'total_score',
        'average_score',
        'material_count',
    ];


}
