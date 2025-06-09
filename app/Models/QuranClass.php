<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuranClass extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'quran_level_id', 'teacher_id', 'description'];

    /**
     * العلاقة مع المستوى القرآني.
     */
    public function quranLevel()
    {
        return $this->belongsTo(QuranLevel::class);
    }

    /**
     * الطلاب النشطين في هذه الحلقة (بدون المحذوفين من العلاقة).
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_quran_classes')
                    ->withTimestamps()
                    ->withPivot(['status', 'joined_at', 'completed_at', 'deleted_at'])
                    ->wherePivotNull('deleted_at');
    }

    /**
     * جميع الطلاب في الحلقة (بما فيهم المحذوفين من العلاقة).
     */
    public function allStudents()
    {
        return $this->belongsToMany(User::class, 'student_quran_classes')
                    ->withTimestamps()
                    ->withPivot(['status', 'joined_at', 'completed_at', 'deleted_at']);
    }

    /**
     * العلاقة مع المعلم.
     */
    public function quranTeacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    /**
     * تحقق مما إذا كان الطالب مسجلاً في هذه الحلقة.
     */
    public function isStudentEnrolled($userId)
    {
        return $this->students()->where('user_id', $userId)->exists();
    }
    /**
     * تحقق مما إذا كان الطالب مسجلاً في هذه الحلقة بنشاط.
     */
    public function isStudentActive($userId)
    {
        return $this->students()->where('user_id', $userId)
                                ->where('status', 'active')
                                ->exists();
    }
    /**
     * حساب عدد الطلاب في هذه الحلقة.
     */
    public function studentCount()
    {
        return $this->students()->count();
    }
    /**
     * الحصول على الطلاب النشطين في هذه الحلقة.
     */
    public function activeStudents()
    {
        return $this->students()->where('status', 'active')->get();
    }
    /**
     * الحصول على الطلاب المكتملين في هذه الحلقة.
     */
    public function completedStudents()
    {
        return $this->students()->where('status', 'completed')->get();
    }
    /**
     * الحصول على الطلاب المنسحبين من هذه الحلقة.
     */
    public function withdrawnStudents()
    {
        return $this->students()->where('status', 'withdrawn')->get();
    }
    /**
     * الحصول على الطلاب الموقوفين مؤقتاً في هذه الحلقة.
     */
    public function suspendedStudents()
    {
        return $this->students()->where('status', 'suspended')->get();
    }
    /**
     * الحصول على الطلاب الذين أكملوا المستوى القرآني.
     */
    public function studentsWhoCompletedLevel()
    {
        return $this->students()->where('status', 'completed')
                                ->whereHas('quranLevel', function ($query) {
                                    $query->where('id', $this->quran_level_id);
                                })->get();
    }
    /**
     * الحصول على الطلاب الذين لم يكملوا المستوى القرآني.
     */
    public function studentsWhoDidNotCompleteLevel()
    {
        return $this->students()->where('status', '!=', 'completed')
                                ->whereHas('quranLevel', function ($query) {
                                    $query->where('id', $this->quran_level_id);
                                })->get();
    }
    /**
     * الحصول على الطلاب الذين أكملوا المستوى القرآني بنجاح.
     */
    public function studentsWhoSuccessfullyCompletedLevel()
    {
        return $this->students()->where('status', 'completed')
                                ->whereHas('quranLevel', function ($query) {
                                    $query->where('id', $this->quran_level_id)
                                          ->where('is_successful', true);
                                })->get();
    }
    /**
     * الحصول على الطلاب الذين لم يكملوا المستوى القرآني بنجاح.
     */
    public function studentsWhoDidNotSuccessfullyCompleteLevel()
    {
        return $this->students()->where('status', '!=', 'completed')
                                ->whereHas('quranLevel', function ($query) {
                                    $query->where('id', $this->quran_level_id)
                                          ->where('is_successful', false);
                                })->get();
    }
    /**
     * الحصول على الطلاب الذين أكملوا المستوى القرآني في فترة زمنية معينة.
     */
    public function studentsWhoCompletedLevelInPeriod($startDate, $endDate)
    {
        return $this->students()->where('status', 'completed')
                                ->whereHas('quranLevel', function ($query) {
                                    $query->where('id', $this->quran_level_id);
                                })
                                ->wherePivot('completed_at', '>=', $startDate)
                                ->wherePivot('completed_at', '<=', $endDate)
                                ->get();
    }
    /**
     * الحصول على الطلاب الذين لم يكملوا المستوى القرآني في فترة زمنية معينة.
     */
    public function studentsWhoDidNotCompleteLevelInPeriod($startDate, $endDate)
    {
        return $this->students()->where('status', '!=', 'completed')
                                ->whereHas('quranLevel', function ($query) {
                                    $query->where('id', $this->quran_level_id);
                                })
                                ->wherePivot('joined_at', '>=', $startDate)
                                ->wherePivot('joined_at', '<=', $endDate)
                                ->get();
    }
    

    

}
