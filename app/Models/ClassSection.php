<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClassSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'grade_id'];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    // العلاقة مع الأنشطة
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'student_class_section', 'class_section_id', 'user_id')->withTimestamps();
    // }

     public function users()
    {
        return $this->belongsToMany(User::class, 'student_class_section')
                    ->withTimestamps()
                    ->withPivot('deleted_at') // إضافة هذا للسماح بالتعامل مع soft delete في pivot
                    ->wherePivotNull('deleted_at'); // إخفاء المحذوفين لينا
    }

    public function allUsers()
    {
        return $this->belongsToMany(User::class, 'student_class_section')
                    ->withTimestamps()
                    ->withPivot('deleted_at'); // تظهر كل العلاقات بما فيها المحذوفة
    }

    public function teachingAssignments()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }

    
}
