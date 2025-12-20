<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['estimation_id', 'name', 'order'];

    public function estimation()
    {
        return $this->belongsTo(Estimation::class);
    }

    public function blocks()
    {
        return $this->belongsToMany(Block::class, 'page_block')
            ->using(PageBlock::class)
            ->withPivot(['quantity', 'price_programming', 'price_integration', 'price_field_creation', 'price_content_management', 'order'])
            ->withTimestamps();
    }
}
