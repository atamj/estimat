<?php

namespace App\Models;

use App\Enums\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Template extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'setup_id',
        'project_type_id',
        'currency',
        'translation_enabled',
        'translation_type',
        'translation_fixed_price',
        'translation_fixed_hours',
        'translation_percentage',
        'translation_languages_count',
    ];

    protected function casts(): array
    {
        return [
            'translation_enabled' => 'boolean',
            'translation_fixed_price' => 'float',
            'translation_fixed_hours' => 'float',
            'translation_percentage' => 'float',
            'translation_languages_count' => 'integer',
            'project_type_id' => 'integer',
        ];
    }

    public function getCurrencySymbolAttribute(): string
    {
        $enum = Currency::tryFrom($this->currency ?? 'EUR');

        return $enum?->symbol() ?? '€';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setup(): BelongsTo
    {
        return $this->belongsTo(Setup::class);
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(TemplatePage::class)->orderBy('order');
    }

    public function regularPages(): HasMany
    {
        return $this->hasMany(TemplatePage::class)->where('type', 'regular')->orderBy('order');
    }

    public function headerPage(): HasOne
    {
        return $this->hasOne(TemplatePage::class)->where('type', 'header');
    }

    public function footerPage(): HasOne
    {
        return $this->hasOne(TemplatePage::class)->where('type', 'footer');
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'template_addon', 'template_id', 'option_id');
    }
}
