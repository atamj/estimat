<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAuthenticatedUser;
use Illuminate\Database\Eloquent\Model;

class Setup extends Model
{
    use ScopedToAuthenticatedUser;

    protected $fillable = ['type', 'fixed_hours', 'project_type_id', 'user_id'];

    public function prices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SetupPrice::class);
    }

    public function priceForCurrency(string $currency): float
    {
        return (float) ($this->prices->firstWhere('currency', $currency)?->price ?? 0);
    }

    public function projectType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
