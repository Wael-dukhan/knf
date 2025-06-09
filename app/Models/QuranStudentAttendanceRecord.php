<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranStudentAttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'quran_class_id',
        'school_id',
        'term_id',
        'date',
        'status',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function quranClass()
    {
        return $this->belongsTo(QuranClass::class, 'quran_class_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }
}
