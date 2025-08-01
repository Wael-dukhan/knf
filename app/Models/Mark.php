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
        'oral_mark',
        'homework_mark',
        'study_mark',
        'work_total',
        'first_term_exam',
        'first_term_total',
        'term_id',
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
}
