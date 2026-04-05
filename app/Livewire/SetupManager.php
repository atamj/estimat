<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Models\ProjectType;
use App\Models\Setup;
use App\Models\SetupPrice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SetupManager extends Component
{
    public string $type = '';

    public ?float $fixed_hours = null;

    public ?int $project_type_id = null;

    public ?int $editingSetupId = null;

    public bool $showForm = false;

    public bool $showHoursField = false;

    public bool $showPriceForm = false;

    /** @var array<int, array{currency: string, price: float}> */
    public array $prices = [];

    public string $newPriceCurrency = 'EUR';

    public string $newPriceAmount = '';

    protected $rules = [
        'type' => 'required|string|max:255',
        'fixed_hours' => 'nullable|numeric|min:0',
        'project_type_id' => 'nullable|exists:project_types,id',
    ];

    public function mount(): void
    {
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
        $this->initNewPriceCurrency();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'fixed_hours' => $this->showHoursField ? ($this->fixed_hours ?? 0) : 0,
            'project_type_id' => $this->project_type_id ?: null,
            'user_id' => Auth::id(),
        ];

        if ($this->editingSetupId) {
            $setup = Setup::findOrFail($this->editingSetupId);
            $setup->update($data);
        } else {
            $setup = Setup::create($data);
        }

        // Sync prices
        $setup->prices()->delete();
        foreach ($this->prices as $priceData) {
            SetupPrice::create([
                'setup_id' => $setup->id,
                'currency' => $priceData['currency'],
                'price' => $priceData['price'],
            ]);
        }

        $this->resetFields();
        session()->flash('message', $this->editingSetupId ? 'Base technique mise à jour.' : 'Base technique créée.');
    }

    public function addPrice(): void
    {
        $this->validate([
            'newPriceAmount' => 'required|numeric|min:0.01',
        ]);

        $usedCurrencies = array_column($this->prices, 'currency');
        if (in_array($this->newPriceCurrency, $usedCurrencies)) {
            $this->addError('newPriceCurrency', 'Un prix existe déjà pour cette devise.');

            return;
        }

        $this->prices[] = ['currency' => $this->newPriceCurrency, 'price' => (float) $this->newPriceAmount];
        $this->showPriceForm = false;
        $this->newPriceAmount = '';
        $this->initNewPriceCurrency();
    }

    public function removePrice(int $index): void
    {
        unset($this->prices[$index]);
        $this->prices = array_values($this->prices);
        $this->initNewPriceCurrency();
    }

    public function initNewPriceCurrency(): void
    {
        $usedCurrencies = array_column($this->prices, 'currency');
        $userDefault = Auth::user()?->default_currency ?? 'EUR';

        if (! in_array($userDefault, $usedCurrencies)) {
            $this->newPriceCurrency = $userDefault;

            return;
        }

        foreach (Currency::cases() as $currency) {
            if (! in_array($currency->value, $usedCurrencies)) {
                $this->newPriceCurrency = $currency->value;

                return;
            }
        }
    }

    public function availableCurrencies(): array
    {
        $usedCurrencies = array_column($this->prices, 'currency');

        return array_filter(Currency::cases(), fn($c) => ! in_array($c->value, $usedCurrencies));
    }

    public function edit(int $id): void
    {
        $setup = Setup::with('prices')->findOrFail($id);
        $this->editingSetupId = $id;
        $this->type = $setup->type;
        $this->fixed_hours = $setup->fixed_hours > 0 ? $setup->fixed_hours : null;
        $this->showHoursField = $setup->fixed_hours > 0;
        $this->project_type_id = $setup->project_type_id;
        $this->prices = $setup->prices->map(fn($p) => ['currency' => $p->currency, 'price' => $p->price])->toArray();
        $this->showForm = true;
        $this->showPriceForm = false;
        $this->initNewPriceCurrency();
    }

    public function delete(int $id): void
    {
        Setup::findOrFail($id)->delete();
        session()->flash('message', 'Base technique supprimée.');
    }

    public function duplicate(int $id): void
    {
        $setup = Setup::with('prices')->findOrFail($id);
        $newSetup = $setup->replicate();
        $newSetup->type .= ' (Copie)';
        $newSetup->save();

        foreach ($setup->prices as $price) {
            SetupPrice::create([
                'setup_id' => $newSetup->id,
                'currency' => $price->currency,
                'price' => $price->price,
            ]);
        }

        session()->flash('message', 'Base technique dupliquée avec succès.');
    }

    public function resetFields(): void
    {
        $this->reset(['type', 'fixed_hours', 'project_type_id', 'editingSetupId', 'showForm', 'showHoursField', 'showPriceForm', 'prices', 'newPriceAmount']);
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
        $this->initNewPriceCurrency();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.setup-manager', [
            'setups' => Setup::with(['projectType', 'prices'])->get(),
            'projectTypes' => ProjectType::all(),
            'currencies' => Currency::cases(),
        ]);
    }
}
