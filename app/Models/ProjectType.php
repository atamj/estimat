<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAuthenticatedUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectType extends Model
{
    use ScopedToAuthenticatedUser;

    protected $fillable = ['name', 'icon', 'description', 'is_default', 'user_id'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($projectType) {
            if ($projectType->is_default) {
                // S'assurer qu'un seul type de projet est par défaut pour cet utilisateur
                static::where('id', '!=', $projectType->id)
                    ->where('user_id', $projectType->user_id)
                    ->update(['is_default' => false]);
            }
        });
    }

    public static function getAvailableIcons()
    {
        return [
            'fab-wordpress' => 'WordPress',
            'fab-laravel' => 'Laravel',
            'fab-php' => 'PHP',
            'fab-js' => 'JavaScript',
            'fab-vuejs' => 'Vue.js',
            'fab-react' => 'React',
            'fab-angular' => 'Angular',
            'fab-symfony' => 'Symfony',
            'fab-drupal' => 'Drupal',
            'fab-joomla' => 'Joomla',
            'fab-magento' => 'Magento',
            'fab-shopify' => 'Shopify',
            'fab-python' => 'Python',
            'fab-node-js' => 'Node.js',
            'fab-docker' => 'Docker',
            'fab-aws' => 'AWS',
            'fas-code' => 'Code / Custom',
            'fas-globe' => 'Web / CMS',
            'fas-mobile-alt' => 'Mobile',
            'fas-server' => 'Backend',
            'fas-paint-brush' => 'Frontend',
            'fas-database' => 'Database',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function translationConfigs()
    {
        return $this->hasMany(TranslationConfig::class);
    }

    public function estimations()
    {
        return $this->hasMany(Estimation::class);
    }
}
