<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Services\EstimationCalculator;

class Estimation extends Model
{
    protected $fillable = [
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

    public function getHasContentAttribute()
    {
        $calculator = new EstimationCalculator();
        $totals = $calculator->calculateTotals($this);
        return ($totals['total_price'] > 0 || ($this->type === 'hour' && $totals['total_time'] > 0));
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

    public function addons()
    {
        return $this->belongsToMany(Option::class, 'estimation_addon', 'estimation_id', 'option_id')
            ->withTimestamps();
    }
}
