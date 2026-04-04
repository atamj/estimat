<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'project_type_id',
    ];

    public function priceSets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BlockPriceSet::class);
    }

    public function priceSetFor(string $currency): ?BlockPriceSet
    {
        return $this->priceSets->firstWhere('currency', $currency);
    }

    public function projectType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'page_block')
            ->using(PageBlock::class)
            ->withPivot(['quantity', 'price_programming', 'price_integration', 'price_field_creation', 'price_content_management', 'order'])
            ->withTimestamps();
    }
}
