<?php

// app/Models/Activity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = ['class_section_id', 'name', 'type', 'description'];

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }
}
