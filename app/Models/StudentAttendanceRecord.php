<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAttendanceRecord extends Model
{
    use HasFactory;

    // public $table = '';

    protected $fillable = [
        'user_id',
        'class_section_id',
        'term_id',
        'date',
        'status',
        'notes',
        'recorded_by',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
