<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    protected $fillable = ['name', 'icon', 'description'];

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
