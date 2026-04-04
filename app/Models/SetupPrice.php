<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupPrice extends Model
{
    protected $fillable = ['setup_id', 'currency', 'price'];

    protected function casts(): array
    {
        return [
            'price' => 'float',
        ];
    }

    public function setup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Setup::class);
    }
}
