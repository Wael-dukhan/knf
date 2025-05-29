<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_id'];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    


}

