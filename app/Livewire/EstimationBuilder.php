<?php

namespace App\Livewire;

use App\Models\Estimation;
use App\Models\Page;
use App\Models\Block;
use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\Option;
use App\Models\TranslationConfig;
use App\Services\EstimationCalculator;
use Livewire\Component;

class EstimationBuilder extends Component
{
    public $estimation;
    public $client_name, $project_name, $hourly_rate, $type, $setup_id, $project_type_id, $translation_enabled, $translation_type, $translation_fixed_price, $translation_fixed_hours, $translation_percentage, $translation_languages_count, $totals;

    // Champs de verrouillage
    public $isPriceLocked = false;
    public $isHoursLocked = false;
    public $isPercentageLocked = false;

    // Modal nouveau bloc
    public $showBlockModal = false;
    public $selectedPageIdForNewBlock = null;
    public $newBlock = [
        'name' => '',
        'description' => '',
        'type_unit' => 'hour',
        'price_programming' => 0,
        'price_integration' => 0,
        'price_field_creation' => 0,
        'price_content_management' => 0,
        'project_type_id' => null,
    ];

    // Modal nouvelle base technique
    public $showSetupModal = false;
    public $newSetup = [
        'type' => '',
        'fixed_price' => 0,
        'fixed_hours' => 0,
        'project_type_id' => null,
    ];

    public $isSetupEditing = false;

    protected $rules = [
        'client_name' => 'required|string|max:255',
        'project_name' => 'nullable|string|max:255',
        'hourly_rate' => 'nullable|numeric|min:0',
        'type' => 'required|in:hour,fixed',
        'setup_id' => 'nullable|exists:setups,id',
        'project_type_id' => 'nullable|exists:project_types,id',
        'translation_enabled' => 'boolean',
        'translation_type' => 'required|in:fixed,hour',
        'translation_fixed_price' => 'nullable|numeric|min:0',
        'translation_fixed_hours' => 'nullable|numeric|min:0',
        'translation_percentage' => 'nullable|numeric|min:0',
        'translation_languages_count' => 'required|integer|min:1',
    ];
    /**
     * @var array|int[]
     */

    public function mount(Estimation $estimation)
    {
        $this->estimation = $estimation;
        $this->client_name = $estimation->client_name;
        $this->project_name = $estimation->project_name;
        $this->hourly_rate = $estimation->hourly_rate;
        $this->type = $estimation->type;
        $this->setup_id = $estimation->setup_id;
        $this->project_type_id = $estimation->project_type_id;
        $this->translation_enabled = $estimation->translation_enabled;
        $this->translation_type = $this->type; // Synchronisé avec le type d'estimation
        $this->translation_fixed_price = $estimation->translation_fixed_price;
        $this->translation_fixed_hours = $estimation->translation_fixed_hours;
        $this->translation_percentage = $estimation->translation_percentage;
        $this->translation_languages_count = $estimation->translation_languages_count ?? 1;

        $this->checkTranslationLock();

        $this->newBlock['project_type_id'] = $this->project_type_id;
        $this->newSetup['project_type_id'] = $this->project_type_id;

        $this->totals = [
            'setup' => 0,
            'programming' => 0,
            'integration' => 0,
            'field_creation' => 0,
            'content_management' => 0,
            'translation' => 0,
            'addons' => 0,
            'total_time' => 0,
            'total_price' => 0,
        ];

        $this->calculate();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        $fields = [
            'client_name', 'project_name', 'hourly_rate', 'type', 'setup_id', 'project_type_id',
            'translation_enabled', 'translation_type', 'translation_fixed_price', 'translation_fixed_hours',
            'translation_percentage', 'translation_languages_count'
        ];

        if (in_array($propertyName, $fields)) {
            $value = $this->{$propertyName};

            // Convertir les chaînes vides en null pour les clés étrangères
            if (in_array($propertyName, ['project_type_id', 'setup_id']) && $value === '') {
                $value = null;
                $this->{$propertyName} = null;
            }

            $this->estimation->update([
                $propertyName => $value
            ]);

            if ($propertyName === 'project_type_id') {
                $this->newBlock['project_type_id'] = $this->project_type_id;
                $this->newSetup['project_type_id'] = $this->project_type_id;
                $this->updateTranslationDefaults();
            }

            if ($propertyName === 'type') {
                // Synchroniser translation_type avec le type d'estimation
                $this->translation_type = $this->type;
                $this->estimation->update(['translation_type' => $this->translation_type]);
                $this->checkTranslationLock();
            }

            // Mise à jour de la configuration globale si modification dans le builder
            if (in_array($propertyName, ['translation_fixed_price', 'translation_fixed_hours', 'translation_percentage'])) {
                $this->syncWithGlobalTranslationConfig($propertyName);
                $this->checkTranslationLock();
            }
        }

        $this->calculate();
    }

    public function syncWithGlobalTranslationConfig($propertyName)
    {
        $config = TranslationConfig::where('project_type_id', $this->project_type_id)->first();
        if (!$config && !$this->project_type_id) {
            $config = TranslationConfig::whereNull('project_type_id')->first();
        }

        if ($config) {
            $mapping = [
                'translation_fixed_price' => 'default_fixed_price',
                'translation_fixed_hours' => 'default_fixed_hours',
                'translation_percentage' => 'default_percentage',
            ];

            if (isset($mapping[$propertyName])) {
                $config->update([
                    $mapping[$propertyName] => $this->{$propertyName}
                ]);
            }
        } else if ($this->project_type_id) {
            // Créer une config si elle n'existe pas pour ce type de projet
            TranslationConfig::create([
                'project_type_id' => $this->project_type_id,
                'default_fixed_price' => $this->translation_fixed_price ?? 0,
                'default_fixed_hours' => $this->translation_fixed_hours ?? 0,
                'default_percentage' => $this->translation_percentage ?? 0,
            ]);
        }
    }

    public function checkTranslationLock()
    {
        $config = TranslationConfig::where('project_type_id', $this->project_type_id)->first();
        if (!$config) {
            $config = TranslationConfig::whereNull('project_type_id')->first();
        }

        if ($config) {
            $this->isPercentageLocked = $config->default_percentage > 0;
            $this->isPriceLocked = $config->default_fixed_price > 0;
            $this->isHoursLocked = $config->default_fixed_hours > 0;
        } else {
            $this->isPriceLocked = false;
            $this->isHoursLocked = false;
            $this->isPercentageLocked = false;
        }
    }

    public function updateTranslationDefaults()
    {
        $config = TranslationConfig::where('project_type_id', $this->project_type_id)->first();

        // Si pas de config spécifique, on prend la config générique (project_type_id is null)
        if (!$config) {
            $config = TranslationConfig::whereNull('project_type_id')->first();
        }

        if ($config) {
            // translation_type sera automatiquement géré par le moteur de calcul selon le champ 'type' de l'estimation
            $this->translation_fixed_price = $config->default_fixed_price;
            $this->translation_fixed_hours = $config->default_fixed_hours;
            $this->translation_percentage = $config->default_percentage;

            $this->estimation->update([
                'translation_fixed_price' => $this->translation_fixed_price,
                'translation_fixed_hours' => $this->translation_fixed_hours,
                'translation_percentage' => $this->translation_percentage,
            ]);

            $this->checkTranslationLock();
        }
    }

    public function calculate()
    {
        $calculator = new EstimationCalculator();
        $this->totals = $calculator->calculateTotals($this->estimation);
    }

    public function addPage()
    {
        $this->estimation->pages()->create([
            'name' => 'Nouvelle Page',
            'order' => $this->estimation->pages()->count() + 1,
        ]);
        $this->estimation->refresh();
        $this->calculate();
    }

    public function deletePage($pageId)
    {
        Page::find($pageId)->delete();
        $this->estimation->refresh();
        $this->calculate();
    }

    public function handleSetupSelection($value)
    {
        if ($value === 'new_setup') {
            $this->showSetupModal = true;
        } else {
            $this->setup_id = $value ?: null;
            $this->estimation->update(['setup_id' => $this->setup_id]);
            $this->calculate();
        }
    }

    public function handleBlockSelection($pageId, $value)
    {
        if ($value === 'new_block') {
            $this->selectedPageIdForNewBlock = $pageId;
            $this->showBlockModal = true;
        } elseif ($value) {
            $this->addBlockToPage($pageId, $value);
        }
    }

    public function addBlockToPage($pageId, $blockId)
    {
        $page = Page::find($pageId);
        $page->blocks()->attach($blockId);
        $this->estimation->refresh();
        $this->calculate();
    }

    public function removeBlockFromPage($pageId, $pivotId)
    {
        $page = Page::find($pageId);
        $page->blocks()->wherePivot('id', $pivotId)->detach();
        $this->estimation->refresh();
        $this->calculate();
    }

    public function updatePivot($pivotId, $field, $value)
    {
        \DB::table('page_block')->where('id', $pivotId)->update([$field => $value]);
        $this->estimation->refresh();
        $this->calculate();
    }

    public function updatePageName($pageId, $name)
    {
        Page::find($pageId)->update(['name' => $name]);
        $this->estimation->refresh();
    }

    public function updatePageQuantity($pageId, $quantity)
    {
        Page::find($pageId)->update(['quantity' => $quantity]);
        $this->estimation->refresh();
        $this->calculate();
    }

    public function toggleAddon($addonId)
    {
        if ($this->estimation->addons()->where('option_id', $addonId)->exists()) {
            $this->estimation->addons()->detach($addonId);
        } else {
            $this->estimation->addons()->attach($addonId);
        }
        $this->estimation->refresh();
        $this->calculate();
    }

    public function createBlock()
    {
        $this->validate([
            'newBlock.name' => 'required|min:3',
            'newBlock.type_unit' => 'required|in:hour,fixed',
        ]);

        $block = Block::create($this->newBlock);

        if ($this->selectedPageIdForNewBlock) {
            $this->addBlockToPage($this->selectedPageIdForNewBlock, $block->id);
        }

        $this->showBlockModal = false;
        $this->selectedPageIdForNewBlock = null;
        $this->reset('newBlock');
        $this->newBlock = [
            'name' => '',
            'description' => '',
            'type_unit' => 'hour',
            'price_programming' => 0,
            'price_integration' => 0,
            'price_field_creation' => 0,
            'price_content_management' => 0,
        ];

        $this->dispatch('block-created');
    }

    public function createSetup()
    {
        $this->validate([
            'newSetup.type' => 'required|min:3',
        ]);

        $setup = Setup::create($this->newSetup);

        $this->setup_id = $setup->id;
        $this->estimation->update(['setup_id' => $setup->id]);

        $this->showSetupModal = false;
        $this->newSetup = [
            'type' => '',
            'fixed_price' => 0,
            'fixed_hours' => 0,
            'project_type_id' => $this->project_type_id,
        ];

        $this->calculate();
        $this->dispatch('setup-created');
    }

    public function toggleSetupEditing()
    {
        $this->isSetupEditing = !$this->isSetupEditing;
    }

    public function updateSetupValue($setupId, $field, $value)
    {
        $setup = Setup::find($setupId);
        if ($setup) {
            $setup->update([$field => $value]);
            $this->calculate();
        }
    }

    public function render()
    {
        $blocksQuery = Block::query();
        $addonsQuery = Option::query();

        if ($this->project_type_id) {
            $blocksQuery->where(function($q) {
                $q->where('project_type_id', $this->project_type_id)
                  ->orWhereNull('project_type_id');
            });
            $addonsQuery->where(function($q) {
                $q->where('project_type_id', $this->project_type_id)
                  ->orWhereNull('project_type_id');
            });
        } else {
            $blocksQuery->whereNull('project_type_id');
            $addonsQuery->whereNull('project_type_id');
        }

        // Filtre selon le type d'estimation et le taux horaire
        if ($this->type === 'fixed') {
            if (!$this->hourly_rate) {
                $blocksQuery->where('type_unit', 'fixed');
            }
        } else { // hour
            if (!$this->hourly_rate) {
                $blocksQuery->where('type_unit', 'hour');
            }
        }

        return view('livewire.estimation-builder', [
            'setups' => Setup::where(function($q) {
                if ($this->project_type_id) {
                    $q->where('project_type_id', $this->project_type_id)
                      ->orWhereNull('project_type_id');
                } else {
                    $q->whereNull('project_type_id');
                }
            })->get(),
            'projectTypes' => ProjectType::all(),
            'availableBlocks' => $blocksQuery->get(),
            'addons' => $addonsQuery->get(),
        ]);
    }
}
