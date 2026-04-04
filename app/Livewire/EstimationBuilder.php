<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\Estimation;
use App\Models\Option;
use App\Models\Page;
use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\TranslationConfig;
use App\Services\EstimationCalculator;
use Livewire\Component;

class EstimationBuilder extends Component
{
    public $estimation;

    public $client_name;

    public $project_name;

    public $hourly_rate;

    public $type;

    public $setup_id;

    public $project_type_id;

    public $translation_enabled;

    public $translation_type;

    public $translation_fixed_price;

    public $translation_fixed_hours;

    public $translation_percentage;

    public $translation_languages_count;

    public $totals;

    public string $currencySymbol = '€';

    public $blockSearch = '';

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

        $this->currencySymbol = $estimation->currency_symbol;

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
            'translation_percentage', 'translation_languages_count',
        ];

        if (in_array($propertyName, $fields)) {
            $value = $this->{$propertyName};

            // Convertir les chaînes vides en null pour les clés étrangères
            if (in_array($propertyName, ['project_type_id', 'setup_id']) && $value === '') {
                $value = null;
                $this->{$propertyName} = null;
            }

            $user = auth()->user();
            $subscription = $user?->activeSubscription;
            $plan = $subscription?->plan;

            if ($propertyName === 'translation_enabled' && $this->translation_enabled) {
                if ($plan && ! $plan->has_translation_module) {
                    $this->translation_enabled = false;
                    $this->addError('translation_enabled', 'Le module de traduction est réservé au plan Pro.');

                    return;
                }
            }

            $this->estimation->update([
                $propertyName => $value,
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
        if (! $config && ! $this->project_type_id) {
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
                    $mapping[$propertyName] => $this->{$propertyName},
                ]);
            }
        } elseif ($this->project_type_id) {
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
        if (! $config) {
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
        if (! $config) {
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
        $calculator = new EstimationCalculator;
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
        $page = Page::find($pageId);
        if ($page && $page->type === 'regular') {
            $page->delete();
            $this->estimation->refresh();
            $this->calculate();
        }
    }

    public function handleSetupSelection($value)
    {
        if ($value === 'new_setup') {
            $this->showSetupModal = true;
        } else {
            $this->setup_id = $value ?: null;
            $this->estimation->update(['setup_id' => $this->setup_id]);
            $this->estimation->refresh();
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
        $page = Page::findOrFail($pageId);
        $order = ($page->blocks()->max('page_block.order') ?? 0) + 1;
        $page->blocks()->attach($blockId, ['order' => $order]);
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
        Page::findOrFail($pageId)->update(['name' => $name]);
        $this->estimation->refresh();
    }

    public function movePage($pageId, $direction)
    {
        $page = Page::find($pageId);
        if (! $page || $page->type !== 'regular') {
            return;
        }

        $pages = $this->estimation->regularPages;
        $currentIndex = $pages->search(fn ($p) => $p->id === $pageId);

        if ($direction === 'up' && $currentIndex > 0) {
            $otherPage = $pages[$currentIndex - 1];
        } elseif ($direction === 'down' && $currentIndex < $pages->count() - 1) {
            $otherPage = $pages[$currentIndex + 1];
        } else {
            return;
        }

        $oldOrder = $page->order;
        $page->update(['order' => $otherPage->order]);
        $otherPage->update(['order' => $oldOrder]);

        $this->estimation->refresh();
    }

    public function moveBlock($pageId, $pivotId, $direction)
    {
        $page = Page::find($pageId);
        if (! $page) {
            return;
        }

        $blocks = $page->blocks()->orderBy('page_block.order')->get();
        $currentIndex = $blocks->search(fn ($b) => $b->pivot->id == $pivotId);

        if ($direction === 'up' && $currentIndex > 0) {
            $otherBlock = $blocks[$currentIndex - 1];
        } elseif ($direction === 'down' && $currentIndex < $blocks->count() - 1) {
            $otherBlock = $blocks[$currentIndex + 1];
        } else {
            return;
        }

        $oldOrder = $blocks[$currentIndex]->pivot->order;
        \DB::table('page_block')->where('id', $pivotId)->update(['order' => $otherBlock->pivot->order]);
        \DB::table('page_block')->where('id', $otherBlock->pivot->id)->update(['order' => $oldOrder]);

        $this->estimation->refresh();
    }

    public function updatePageQuantity($pageId, $quantity)
    {
        $page = Page::find($pageId);
        if ($page && $page->type === 'regular') {
            $page->update(['quantity' => $quantity]);
            $this->estimation->refresh();
            $this->calculate();
        }
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

        $user = auth()->user();
        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;

        if ($plan && $plan->max_blocks !== -1) {
            $count = Block::where('user_id', $user->id)->count();
            if ($count >= $plan->max_blocks) {
                $this->addError('newBlock.name', 'Vous avez atteint la limite de blocs de votre plan.');

                return;
            }
        }

        $data = array_merge($this->newBlock, ['user_id' => $user?->id]);
        $block = Block::create($data);

        if ($this->selectedPageIdForNewBlock) {
            $this->addBlockToPage($this->selectedPageIdForNewBlock, $block->id);
        }

        $this->showBlockModal = false;
        $this->selectedPageIdForNewBlock = null;
        $this->newBlock = ['name' => '', 'description' => '', 'project_type_id' => null];

        $this->dispatch('block-created');
    }

    public function createSetup()
    {
        $this->validate([
            'newSetup.type' => 'required|min:3',
        ]);

        $setup = Setup::create(array_merge($this->newSetup, ['user_id' => auth()->id()]));

        $this->setup_id = $setup->id;
        $this->estimation->update(['setup_id' => $setup->id]);
        $this->estimation->refresh();

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
        $this->isSetupEditing = ! $this->isSetupEditing;
    }

    public function updateSetupValue($setupId, string $field, $value): void
    {
        $setup = Setup::find($setupId);
        if ($setup && $field === 'fixed_hours') {
            $setup->update(['fixed_hours' => $value]);
            $this->calculate();
        }
    }

    public function render()
    {
        $blocksQuery = Block::query();
        $addonsQuery = Option::query();

        if ($this->project_type_id) {
            $blocksQuery->where(function ($q) {
                $q->where('project_type_id', $this->project_type_id)
                    ->orWhereNull('project_type_id');
            });
            $addonsQuery->where(function ($q) {
                $q->where('project_type_id', $this->project_type_id)
                    ->orWhereNull('project_type_id');
            });
        } else {
            $blocksQuery->whereNull('project_type_id');
            $addonsQuery->whereNull('project_type_id');
        }

        // Filtre selon le type d'estimation et le taux horaire
        if (! $this->hourly_rate) {
            $currencyKey = $this->type === 'hour' ? 'HOUR' : ($this->estimation->currency ?? 'EUR');
            $blocksQuery->whereHas('priceSets', fn ($q) => $q->where('currency', $currencyKey));

            if ($this->type === 'fixed') {
                $addonsQuery->whereIn('type', ['fixed_price', 'percentage']);
            } else {
                $addonsQuery->whereIn('type', ['fixed_hours', 'percentage']);
            }
        }

        if ($this->blockSearch) {
            $blocksQuery->where('name', 'like', '%'.$this->blockSearch.'%');
        }

        return view('livewire.estimation-builder', [
            'setups' => Setup::where('user_id', auth()->id())->where(function ($q) {
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
