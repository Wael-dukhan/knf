<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StudentClassSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'class_section_id','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }
    
}
