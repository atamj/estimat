<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplatePage extends Model
{
    protected $fillable = ['template_id', 'name', 'quantity', 'order', 'type'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(TemplatePageBlock::class)->orderBy('order');
    }
}
