<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'grade_id',
        'main_book_path',
        'activity_book_path',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function academicYear()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }
    public function term(){
        return $this->belongsTo(Term::class);
    }
    public function classSection()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }
    public function materialTeacherAssignments()
    {
        return $this->hasMany(MaterialTeacherAssignment::class);
    }
    public function materialUserTermSections()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(MaterialUserTermSection::class);
    }


}
