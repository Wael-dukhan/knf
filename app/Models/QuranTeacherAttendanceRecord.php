<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranTeacherAttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'school_id',
        'term_id',
        'date',
        'status',
        'notes'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
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
