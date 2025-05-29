<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','location'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function teachers()
    {
        return $this->hasMany(User::class)->where('role', 'teacher');
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    public function managers()
    {
        return $this->hasMany(User::class)->where('role', 'manager');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function academicYear()
    {
        return $this->hasMany(AcademicYear::class);
    }

}
