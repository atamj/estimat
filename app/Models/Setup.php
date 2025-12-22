<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setup extends Model
{
    protected $fillable = ['type', 'fixed_price', 'fixed_hours', 'project_type_id', 'user_id'];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }
}
