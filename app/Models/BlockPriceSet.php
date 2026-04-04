<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockPriceSet extends Model
{
    protected $fillable = [
        'block_id',
        'currency',
        'price_programming',
        'price_integration',
        'price_field_creation',
        'price_content_management',
    ];

    protected function casts(): array
    {
        return [
            'price_programming' => 'float',
            'price_integration' => 'float',
            'price_field_creation' => 'float',
            'price_content_management' => 'float',
        ];
    }

    public function block(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
