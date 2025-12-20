<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type_unit',
        'price_programming',
        'price_integration',
        'price_field_creation',
        'price_content_management',
        'project_type_id',
    ];

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_block')
            ->using(PageBlock::class)
            ->withPivot(['quantity', 'price_programming', 'price_integration', 'price_field_creation', 'price_content_management', 'order'])
            ->withTimestamps();
    }
}
