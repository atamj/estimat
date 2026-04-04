<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplatePageBlock extends Model
{
    protected $fillable = [
        'template_page_id',
        'block_id',
        'quantity',
        'order',
        'price_programming',
        'price_integration',
        'price_field_creation',
        'price_content_management',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'order' => 'integer',
            'price_programming' => 'float',
            'price_integration' => 'float',
            'price_field_creation' => 'float',
            'price_content_management' => 'float',
        ];
    }

    public function templatePage(): BelongsTo
    {
        return $this->belongsTo(TemplatePage::class);
    }

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
