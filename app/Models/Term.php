<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

}
