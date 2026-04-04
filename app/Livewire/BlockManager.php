<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Models\Block;
use App\Models\BlockPriceSet;
use App\Models\ProjectType;
use Livewire\Component;
use Livewire\WithPagination;

class BlockManager extends Component
{
    use WithPagination;

    public string $name = '';

    public string $description = '';

    public ?int $project_type_id = null;

    public ?int $editingBlockId = null;

    public bool $showForm = false;

    public string $filter_project_type = '';

    /** @var array<int, array{currency: string, price_programming: float, price_integration: float, price_field_creation: float, price_content_management: float}> */
    public array $priceSets = [];

    public bool $showHoursForm = false;

    public bool $showPriceSetForm = false;

    public string $newPriceSetCurrency = 'EUR';

    /** @var array{price_programming: string, price_integration: string, price_field_creation: string, price_content_management: string} */
    public array $newPriceSetValues = [
        'price_programming' => '',
        'price_integration' => '',
        'price_field_creation' => '',
        'price_content_management' => '',
    ];

    /** @var array{price_programming: string, price_integration: string, price_field_creation: string, price_content_management: string} */
    public array $newHoursValues = [
        'price_programming' => '',
        'price_integration' => '',
        'price_field_creation' => '',
        'price_content_management' => '',
    ];

    protected $rules = [
        'name' => 'required|min:3',
        'project_type_id' => 'nullable|exists:project_types,id',
    ];

    public function mount(): void
    {
        $this->project_type_id = ProjectType::where('user_id', auth()->id())->where('is_default', true)->value('id');
        $this->initNewPriceSetCurrency();
    }

    public function updatedFilterProjectType(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate();

        $user = auth()->user();
        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;

        if (! $this->editingBlockId && $plan && $plan->max_blocks !== -1) {
            $count = Block::where('user_id', $user->id)->count();
            if ($count >= $plan->max_blocks) {
                session()->flash('error', 'Vous avez atteint la limite de blocs de votre plan.');

                return;
            }
        }

        $data = [
            'user_id' => $user?->id,
            'name' => $this->name,
            'description' => $this->description,
            'project_type_id' => $this->project_type_id ?: null,
        ];

        if ($this->editingBlockId) {
            $block = Block::findOrFail($this->editingBlockId);
            $block->update($data);
        } else {
            $block = Block::create($data);
        }

        // Sync price sets
        $block->priceSets()->delete();
        foreach ($this->priceSets as $setData) {
            BlockPriceSet::create(array_merge(['block_id' => $block->id], $setData));
        }

        $this->resetFields();
        session()->flash('message', $this->editingBlockId ? 'Bloc mis à jour.' : 'Bloc créé.');
    }

    public function addHours(): void
    {
        $this->validate([
            'newHoursValues.price_programming' => 'required|numeric|min:0',
            'newHoursValues.price_integration' => 'required|numeric|min:0',
            'newHoursValues.price_field_creation' => 'required|numeric|min:0',
            'newHoursValues.price_content_management' => 'required|numeric|min:0',
        ]);

        $this->priceSets = array_filter($this->priceSets, fn ($s) => $s['currency'] !== 'HOUR');
        $this->priceSets = array_values($this->priceSets);
        $this->priceSets[] = array_merge(['currency' => 'HOUR'], array_map('floatval', $this->newHoursValues));

        $this->showHoursForm = false;
        $this->newHoursValues = ['price_programming' => '', 'price_integration' => '', 'price_field_creation' => '', 'price_content_management' => ''];
    }

    public function addPriceSet(): void
    {
        $this->validate([
            'newPriceSetValues.price_programming' => 'required|numeric|min:0',
            'newPriceSetValues.price_integration' => 'required|numeric|min:0',
            'newPriceSetValues.price_field_creation' => 'required|numeric|min:0',
            'newPriceSetValues.price_content_management' => 'required|numeric|min:0',
        ]);

        $usedCurrencies = array_column($this->priceSets, 'currency');
        if (in_array($this->newPriceSetCurrency, $usedCurrencies)) {
            $this->addError('newPriceSetCurrency', 'Un prix existe déjà pour cette devise.');

            return;
        }

        $this->priceSets[] = array_merge(['currency' => $this->newPriceSetCurrency], array_map('floatval', $this->newPriceSetValues));
        $this->showPriceSetForm = false;
        $this->newPriceSetValues = ['price_programming' => '', 'price_integration' => '', 'price_field_creation' => '', 'price_content_management' => ''];
        $this->initNewPriceSetCurrency();
    }

    public function removePriceSet(int $index): void
    {
        unset($this->priceSets[$index]);
        $this->priceSets = array_values($this->priceSets);
        $this->initNewPriceSetCurrency();
    }

    public function initNewPriceSetCurrency(): void
    {
        $usedCurrencies = array_column($this->priceSets, 'currency');
        $userDefault = auth()->user()?->default_currency ?? 'EUR';

        if (! in_array($userDefault, $usedCurrencies)) {
            $this->newPriceSetCurrency = $userDefault;

            return;
        }

        foreach (Currency::cases() as $currency) {
            if (! in_array($currency->value, $usedCurrencies)) {
                $this->newPriceSetCurrency = $currency->value;

                return;
            }
        }
    }

    public function availableCurrencies(): array
    {
        $usedCurrencies = array_column($this->priceSets, 'currency');

        return array_filter(Currency::cases(), fn ($c) => ! in_array($c->value, $usedCurrencies));
    }

    public function hasHoursSet(): bool
    {
        return collect($this->priceSets)->contains('currency', 'HOUR');
    }

    public function edit(int $id): void
    {
        $block = Block::with('priceSets')->findOrFail($id);
        $this->editingBlockId = $id;
        $this->name = $block->name;
        $this->description = $block->description ?? '';
        $this->project_type_id = $block->project_type_id;
        $this->priceSets = $block->priceSets->map(fn ($s) => [
            'currency' => $s->currency,
            'price_programming' => $s->price_programming,
            'price_integration' => $s->price_integration,
            'price_field_creation' => $s->price_field_creation,
            'price_content_management' => $s->price_content_management,
        ])->toArray();
        $this->showForm = true;
        $this->showHoursForm = false;
        $this->showPriceSetForm = false;
        $this->initNewPriceSetCurrency();
    }

    public function delete(int $id): void
    {
        Block::findOrFail($id)->delete();
        session()->flash('message', 'Bloc supprimé.');
    }

    public function duplicate(int $id): void
    {
        $user = auth()->user();
        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;

        if ($plan && $plan->max_blocks !== -1) {
            $count = Block::where('user_id', $user->id)->count();
            if ($count >= $plan->max_blocks) {
                session()->flash('error', 'Vous avez atteint la limite de blocs de votre plan.');

                return;
            }
        }

        $block = Block::with('priceSets')->findOrFail($id);
        $newBlock = $block->replicate();
        $newBlock->name .= ' (Copie)';
        $newBlock->save();

        foreach ($block->priceSets as $priceSet) {
            BlockPriceSet::create([
                'block_id' => $newBlock->id,
                'currency' => $priceSet->currency,
                'price_programming' => $priceSet->price_programming,
                'price_integration' => $priceSet->price_integration,
                'price_field_creation' => $priceSet->price_field_creation,
                'price_content_management' => $priceSet->price_content_management,
            ]);
        }

        session()->flash('message', 'Bloc dupliqué avec succès.');
    }

    public function resetFields(): void
    {
        $this->reset(['name', 'description', 'project_type_id', 'editingBlockId', 'showForm', 'showHoursForm', 'showPriceSetForm', 'priceSets', 'newPriceSetValues', 'newHoursValues']);
        $this->project_type_id = ProjectType::where('user_id', auth()->id())->where('is_default', true)->value('id');
        $this->newPriceSetValues = ['price_programming' => '', 'price_integration' => '', 'price_field_creation' => '', 'price_content_management' => ''];
        $this->newHoursValues = ['price_programming' => '', 'price_integration' => '', 'price_field_creation' => '', 'price_content_management' => ''];
        $this->initNewPriceSetCurrency();
    }

    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();
        $query = Block::query()->where('user_id', $user->id);

        if ($this->filter_project_type !== '') {
            if ($this->filter_project_type === 'null') {
                $query->whereNull('project_type_id');
            } else {
                $query->where('project_type_id', $this->filter_project_type);
            }
        }

        return view('livewire.block-manager', [
            'blocks' => $query->with(['projectType', 'priceSets'])->orderBy('name')->paginate(15),
            'projectTypes' => ProjectType::where('user_id', auth()->id())->get(),
            'currencies' => Currency::cases(),
        ]);
    }
}
