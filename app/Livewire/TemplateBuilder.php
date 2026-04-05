<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\Option;
use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\Template;
use App\Models\TemplatePage;
use App\Models\TemplatePageBlock;
use Livewire\Component;

class TemplateBuilder extends Component
{
    public Template $template;

    public string $name = '';

    public ?string $type = 'hour';

    public ?int $setup_id = null;

    public ?int $project_type_id = null;

    public bool $translation_enabled = false;

    public ?string $translation_type = 'hour';

    public ?float $translation_fixed_price = null;

    public ?float $translation_fixed_hours = null;

    public ?float $translation_percentage = null;

    public int $translation_languages_count = 1;

    public string $currencySymbol = '€';

    public string $blockSearch = '';

    // Modal nouveau bloc
    public bool $showBlockModal = false;

    public ?int $selectedPageIdForNewBlock = null;

    /** @var array{name: string, description: string, project_type_id: int|null} */
    public array $newBlock = [
        'name' => '',
        'description' => '',
        'project_type_id' => null,
    ];

    // Modal nouvelle base technique
    public bool $showSetupModal = false;

    /** @var array{type: string, fixed_price: float, fixed_hours: float, project_type_id: int|null} */
    public array $newSetup = [
        'type' => '',
        'fixed_price' => 0,
        'fixed_hours' => 0,
        'project_type_id' => null,
    ];

    public bool $isSetupEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
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

    public function mount(Template $template): void
    {
        $this->template = $template;
        $this->name = $template->name;
        $this->type = $template->type;
        $this->setup_id = $template->setup_id;
        $this->project_type_id = $template->project_type_id;
        $this->translation_enabled = $template->translation_enabled;
        $this->translation_type = $template->type;
        $this->translation_fixed_price = $template->translation_fixed_price;
        $this->translation_fixed_hours = $template->translation_fixed_hours;
        $this->translation_percentage = $template->translation_percentage;
        $this->translation_languages_count = $template->translation_languages_count ?? 1;
        $this->currencySymbol = $template->currency_symbol;

        $this->newBlock['project_type_id'] = $this->project_type_id;
        $this->newSetup['project_type_id'] = $this->project_type_id;
    }

    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName);

        $fields = [
            'name', 'type', 'setup_id', 'project_type_id',
            'translation_enabled', 'translation_type', 'translation_fixed_price', 'translation_fixed_hours',
            'translation_percentage', 'translation_languages_count',
        ];

        if (in_array($propertyName, $fields)) {
            $value = $this->{$propertyName};

            if (in_array($propertyName, ['project_type_id', 'setup_id']) && $value === '') {
                $value = null;
                $this->{$propertyName} = null;
            }

            $this->template->update([$propertyName => $value]);

            if ($propertyName === 'project_type_id') {
                $this->newBlock['project_type_id'] = $this->project_type_id;
                $this->newSetup['project_type_id'] = $this->project_type_id;
            }

            if ($propertyName === 'type') {
                $this->translation_type = $this->type;
                $this->template->update(['translation_type' => $this->translation_type]);
            }
        }
    }

    public function addPage(): void
    {
        $this->template->pages()->create([
            'name' => 'Nouvelle Page',
            'order' => $this->template->pages()->count() + 1,
        ]);
        $this->template->refresh();
    }

    public function deletePage(int $pageId): void
    {
        $page = TemplatePage::find($pageId);
        if ($page && $page->type === 'regular') {
            $page->delete();
            $this->template->refresh();
        }
    }

    public function handleSetupSelection(string $value): void
    {
        if ($value === 'new_setup') {
            $this->showSetupModal = true;
        } else {
            $this->setup_id = $value ? (int) $value : null;
            $this->template->update(['setup_id' => $this->setup_id]);
            $this->template->refresh();
        }
    }

    public function handleBlockSelection(int $pageId, string $value): void
    {
        if ($value === 'new_block') {
            $this->selectedPageIdForNewBlock = $pageId;
            $this->showBlockModal = true;
        } elseif ($value) {
            $this->addBlockToPage($pageId, (int) $value);
        }
    }

    public function addBlockToPage(int $pageId, int $blockId): void
    {
        $page = TemplatePage::findOrFail($pageId);
        $order = ($page->blocks()->max('order') ?? 0) + 1;
        $page->blocks()->create([
            'block_id' => $blockId,
            'order' => $order,
        ]);
        $this->template->refresh();
    }

    public function removeBlockFromPage(int $pageId, int $blockId): void
    {
        TemplatePageBlock::where('template_page_id', $pageId)
            ->where('id', $blockId)
            ->delete();
        $this->template->refresh();
    }

    public function updateBlockField(int $blockId, string $field, mixed $value): void
    {
        $allowed = ['quantity', 'price_programming', 'price_integration', 'price_field_creation', 'price_content_management'];
        if (in_array($field, $allowed)) {
            TemplatePageBlock::where('id', $blockId)->update([$field => $value]);
            $this->template->refresh();
        }
    }

    public function updatePageName(int $pageId, string $name): void
    {
        TemplatePage::findOrFail($pageId)->update(['name' => $name]);
        $this->template->refresh();
    }

    public function movePage(int $pageId, string $direction): void
    {
        $page = TemplatePage::find($pageId);
        if (! $page || $page->type !== 'regular') {
            return;
        }

        $pages = $this->template->regularPages;
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

        $this->template->refresh();
    }

    public function moveBlock(int $pageId, int $blockId, string $direction): void
    {
        $page = TemplatePage::find($pageId);
        if (! $page) {
            return;
        }

        $blocks = $page->blocks()->orderBy('order')->get();
        $currentIndex = $blocks->search(fn ($b) => $b->id == $blockId);

        if ($direction === 'up' && $currentIndex > 0) {
            $otherBlock = $blocks[$currentIndex - 1];
        } elseif ($direction === 'down' && $currentIndex < $blocks->count() - 1) {
            $otherBlock = $blocks[$currentIndex + 1];
        } else {
            return;
        }

        $oldOrder = $blocks[$currentIndex]->order;
        TemplatePageBlock::where('id', $blockId)->update(['order' => $otherBlock->order]);
        TemplatePageBlock::where('id', $otherBlock->id)->update(['order' => $oldOrder]);

        $this->template->refresh();
    }

    public function updatePageQuantity(int $pageId, int $quantity): void
    {
        $page = TemplatePage::find($pageId);
        if ($page && $page->type === 'regular') {
            $page->update(['quantity' => $quantity]);
            $this->template->refresh();
        }
    }

    public function toggleAddon(int $addonId): void
    {
        if ($this->template->addons()->where('option_id', $addonId)->exists()) {
            $this->template->addons()->detach($addonId);
        } else {
            $this->template->addons()->attach($addonId);
        }
        $this->template->refresh();
    }

    public function createBlock(): void
    {
        $this->validate([
            'newBlock.name' => 'required|min:3',
        ]);

        $user = auth()->user();
        if (! $user) {
            return;
        }

        $data = array_merge($this->newBlock, ['user_id' => $user->id]);
        $block = Block::create($data);

        if ($this->selectedPageIdForNewBlock) {
            $this->addBlockToPage($this->selectedPageIdForNewBlock, $block->id);
        }

        $this->showBlockModal = false;
        $this->selectedPageIdForNewBlock = null;
        $this->newBlock = ['name' => '', 'description' => '', 'project_type_id' => null];

        $this->dispatch('block-created');
    }

    public function createSetup(): void
    {
        $this->validate([
            'newSetup.type' => 'required|min:3',
        ]);

        $setup = Setup::create(array_merge($this->newSetup, ['user_id' => auth()->id()]));

        $this->setup_id = $setup->id;
        $this->template->update(['setup_id' => $setup->id]);
        $this->template->refresh();

        $this->showSetupModal = false;
        $this->newSetup = [
            'type' => '',
            'fixed_price' => 0,
            'fixed_hours' => 0,
            'project_type_id' => $this->project_type_id,
        ];

        $this->dispatch('setup-created');
    }

    public function toggleSetupEditing(): void
    {
        $this->isSetupEditing = ! $this->isSetupEditing;
    }

    public function updateSetupValue(int $setupId, string $field, mixed $value): void
    {
        $setup = Setup::find($setupId);
        if ($setup && $field === 'fixed_hours') {
            $setup->update(['fixed_hours' => $value]);
        }
    }

    public function render(): \Illuminate\View\View
    {
        $blocksQuery = Block::query();
        $addonsQuery = Option::query()->where('user_id', $this->template->user_id);

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

        if ($this->blockSearch) {
            $blocksQuery->where('name', 'like', '%'.$this->blockSearch.'%');
        }

        return view('livewire.template-builder', [
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
