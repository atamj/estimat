<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PageBlock extends Pivot
{
    protected $table = 'page_block';

    protected $fillable = [
        'page_id',
        'block_id',
        'quantity',
        'price_programming',
        'price_integration',
        'price_field_creation',
        'price_content_management',
        'order',
    ];
}
