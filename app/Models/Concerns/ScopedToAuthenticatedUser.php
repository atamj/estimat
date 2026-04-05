<?php

namespace App\Models\Concerns;

use App\Models\Scopes\OwnedByAuthenticatedUserScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait ScopedToAuthenticatedUser
{
    protected static function bootScopedToAuthenticatedUser(): void
    {
        static::addGlobalScope(new OwnedByAuthenticatedUserScope);

        static::creating(function (Model $model): void {
            if ($model->getAttribute('user_id') === null && Auth::check()) {
                $model->setAttribute('user_id', Auth::id());
            }
        });
    }
}
