<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'calculation_base',
        'project_type_id',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function estimations()
    {
        return $this->belongsToMany(Estimation::class, 'estimation_addon', 'option_id', 'estimation_id')
            ->withTimestamps();
    }
}
