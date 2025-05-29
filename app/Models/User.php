<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * الصفات القابلة للتعيين بشكل جماعي.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'school_id',
        'supervisor_id',
        'gender',
    ];

    /**
     * الصفات المخفية عند التوثيق.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * الصفات التي يجب أن يتم تحويلها (casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * علاقة المستخدم بالمدرسة.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * علاقة المستخدم بالطلاب (إذا كان المستخدم هو ولي أمر).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }

    /**
     * علاقة المستخدم بالأولياء (إذا كان المستخدم هو طالب).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    /**
     * علاقة المشرف بالمُعلمين الذين يشرف عليهم.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supervisedTeachers()
    {
        return $this->hasMany(User::class, 'supervisor_id')->whereHas('roles', function ($q) {
            $q->where('name', 'teacher');
        });
    }

    /**
     * علاقة المشرف بالطلاب الذين يشرف عليهم.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supervisedStudents()
    {
        return $this->hasMany(User::class, 'supervisor_id')->whereHas('roles', function ($q) {
            $q->where('name', 'student');
        });
    }

    /**
     * علاقة المستخدم بالمشرف الذي يشرف عليه.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * نطاق لتسهيل البحث عن المستخدمين حسب الدور.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoleIs($query, $role)
    {
        return $query->role($role);
    }


    public function classSections()
    {
        return $this->belongsToMany(ClassSection::class, 'student_class_section', 'user_id', 'class_section_id')
                    ->withTimestamps();
    }

    public function activeClassSections()
    {
        return $this->belongsToMany(ClassSection::class, 'student_class_section', 'user_id', 'class_section_id')
                    ->withPivot('status', 'deleted_at')
                    ->withTimestamps()
                    ->wherePivotNull('deleted_at');
    }


    // public function academicYears()
    // {
    //     return $this->belongsToMany(AcademicYear::class, 'student_academic_year', 'user_id', 'academic_year_id')
    //                 ->withTimestamps();
    // }
    
    public function teachingAssignments()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }

}
