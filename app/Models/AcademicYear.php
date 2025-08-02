<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id', 'start_date', 'end_date','status'];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    
    public function terms()
    {
        return $this->hasMany(\App\Models\Term::class);
    }


}

