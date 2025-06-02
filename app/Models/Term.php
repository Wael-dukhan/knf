<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Term extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'academic_year_id', 'school_id','start_date','end_date'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classSections()
    {
        return $this->hasMany(ClassSection::class);
    }
    
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // public function materilas()
    // {
    //     return $this->hasMany(Material::class);
    // }


    public function teachingAssignments()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }

    
    public static function currentTermId($schoolId = null)
    {
        $today = time();

        $query = Term::with('academicYear')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $term = $query->first();

        return $term?->id; // ترجع null إذا لم يوجد فصل حالي
    }
}
