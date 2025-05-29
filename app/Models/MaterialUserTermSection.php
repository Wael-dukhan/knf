<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialUserTermSection extends Model
{
    protected $table = 'material_user_term_section';

    protected $fillable = ['user_id', 'material_id', 'term_id', 'class_section_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }
}
