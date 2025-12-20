<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationConfig extends Model
{
    protected $fillable = [
        'default_fixed_price',
        'default_fixed_hours',
        'default_percentage',
        'default_type',
        'project_type_id',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }
}
