<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name','school_id', 'academic_year_id','description','grade_level','grade_number'];

        protected $casts = [
        'grade_level' => 'integer',
    ];

    // إضافة التحقق من المراحل الدراسية المسموح بها
    const GRADE_LEVELS = [
        1 => 'المرحلة الابتدائية',
        2 => 'المرحلة الإعدادية',
        3 => 'المرحلة الثانوية',
    ];

    public static function getGrades()
    {
        return [
            1  => __('messages.grades.1'),
            2  => __('messages.grades.2'),
            3  => __('messages.grades.3'),
            4  => __('messages.grades.4'),
            5  => __('messages.grades.5'),
            6  => __('messages.grades.6'),
            7  => __('messages.grades.7'),
            8  => __('messages.grades.8'),
            9  => __('messages.grades.9'),
            10 => __('messages.grades.10'),
            11 => __('messages.grades.11'),
            12 => __('messages.grades.12'),
        ];
    }
    
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

        /**
     * علاقة الصف الدراسي مع الشعب الدراسية.
     */
    public function classSections()
    {
        return $this->hasMany(ClassSection::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }
    public function teachers()
    {
        return $this->hasMany(User::class)->where('role', 'teacher');
    }
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    public function materials()
    {
        return $this->hasMany(Material::class);
    }

}
