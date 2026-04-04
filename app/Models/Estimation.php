<?php

namespace App\Models;

use App\Enums\Currency;
use App\Services\EstimationCalculator;
use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    protected $fillable = [
        'user_id',
        'client_name',
        'project_name',
        'hourly_rate',
        'type',
        'setup_id',
        'translation_enabled',
        'translation_type',
        'translation_fixed_price',
        'translation_fixed_hours',
        'translation_percentage',
        'translation_languages_count',
        'project_type_id',
        'currency',
    ];

    protected $casts = [
        'translation_enabled' => 'boolean',
        'hourly_rate' => 'float',
        'translation_fixed_price' => 'float',
        'translation_fixed_hours' => 'float',
        'translation_percentage' => 'float',
        'translation_languages_count' => 'integer',
        'project_type_id' => 'integer',
    ];

    public function getCurrencySymbolAttribute(): string
    {
        $enum = Currency::tryFrom($this->currency ?? 'EUR');

        return $enum?->symbol() ?? '€';
    }

    public function getTotalPriceAttribute()
    {
        $calculator = new EstimationCalculator;
        $totals = $calculator->calculateTotals($this);

        return $totals['total_price'];
    }

    public function getTotalTimeAttribute()
    {
        $calculator = new EstimationCalculator;
        $totals = $calculator->calculateTotals($this);

        return $totals['total_time'];
    }

    public function getHasContentAttribute()
    {
        $calculator = new EstimationCalculator;
        $totals = $calculator->calculateTotals($this);

        return $totals['total_price'] > 0 || ($this->type === 'hour' && $totals['total_time'] > 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function setup()
    {
        return $this->belongsTo(Setup::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class)->orderBy('order');
    }

    public function regularPages()
    {
        return $this->hasMany(Page::class)->where('type', 'regular')->orderBy('order');
    }

    public function headerPage()
    {
        return $this->hasOne(Page::class)->where('type', 'header');
    }

    public function footerPage()
    {
        return $this->hasOne(Page::class)->where('type', 'footer');
    }

    public function addons()
    {
        return $this->belongsToMany(Option::class, 'estimation_addon', 'estimation_id', 'option_id')
            ->withTimestamps();
    }
}
