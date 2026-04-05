<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAuthenticatedUser;
use Illuminate\Database\Eloquent\Model;

class TranslationConfig extends Model
{
    use ScopedToAuthenticatedUser;

    protected $fillable = [
        'default_fixed_price',
        'default_fixed_hours',
        'default_percentage',
        'default_type',
        'project_type_id',
        'user_id',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
