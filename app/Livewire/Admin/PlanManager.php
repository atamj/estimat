<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Services\StripePlanSyncService;
use Illuminate\Support\Str;
use Livewire\Component;

class PlanManager extends Component
{
    public $plans;

    public $name;

    public $slug;

    public $description;

    public string $billing_type = 'recurring'; // 'recurring' ou 'lifetime'

    public $price_monthly;

    public $price_yearly;

    public $price_lifetime;

    public $stripe_monthly_price_id;

    public $stripe_yearly_price_id;

    public $stripe_lifetime_price_id;

    public $max_estimations = -1;

    public $max_blocks = -1;

    public $has_white_label_pdf = false;

    public $has_translation_module = true;

    public $is_active = true;

    public $editingPlanId = null;

    public $showForm = false;

    protected function rules(): array
    {
        if ($this->billing_type === 'lifetime') {
            return [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'price_lifetime' => 'required|numeric|min:0.01',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
        ];
    }

    public function mount()
    {
        $this->loadPlans();
    }

    public function loadPlans()
    {
        $this->plans = Plan::all();
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function toggleForm()
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->resetFields();
        }
    }

    public function resetFields(): void
    {
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->billing_type = 'recurring';
        $this->price_monthly = 0;
        $this->price_yearly = 0;
        $this->price_lifetime = null;
        $this->stripe_monthly_price_id = null;
        $this->stripe_yearly_price_id = null;
        $this->stripe_lifetime_price_id = null;
        $this->max_estimations = -1;
        $this->max_blocks = -1;
        $this->has_white_label_pdf = false;
        $this->has_translation_module = true;
        $this->is_active = true;
        $this->editingPlanId = null;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_monthly' => $this->billing_type === 'lifetime' ? 0 : $this->price_monthly,
            'price_yearly' => $this->billing_type === 'lifetime' ? 0 : $this->price_yearly,
            'price_lifetime' => $this->billing_type === 'lifetime' ? $this->price_lifetime : null,
            'max_estimations' => $this->max_estimations,
            'max_blocks' => $this->max_blocks,
            'has_white_label_pdf' => $this->has_white_label_pdf,
            'has_translation_module' => $this->has_translation_module,
            'is_active' => $this->is_active,
        ];

        try {
            if ($this->editingPlanId) {
                $plan = Plan::findOrFail($this->editingPlanId);
                $oldPrices = $plan->only(['price_monthly', 'price_yearly', 'price_lifetime']);
                $plan->update($data);
                app(StripePlanSyncService::class)->sync($plan, $oldPrices);
                session()->flash('message', 'Plan mis à jour et synchronisé avec Stripe.');
            } else {
                $plan = Plan::create($data);
                app(StripePlanSyncService::class)->sync($plan);
                session()->flash('message', 'Plan créé et synchronisé avec Stripe.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Plan sauvegardé mais erreur Stripe : '.$e->getMessage());
        }

        $this->resetFields();
        $this->showForm = false;
        $this->loadPlans();
    }

    public function edit($id)
    {
        if ($this->editingPlanId == $id) {
            $this->resetFields();
            $this->showForm = false;

            return;
        }

        $this->resetValidation();
        $plan = Plan::findOrFail($id);

        // On assigne les valeurs une par une pour forcer la réactivité de Livewire
        $this->editingPlanId = $id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->description = $plan->description;
        $this->billing_type = $plan->price_lifetime > 0 ? 'lifetime' : 'recurring';
        $this->price_monthly = $plan->price_monthly;
        $this->price_yearly = $plan->price_yearly;
        $this->price_lifetime = $plan->price_lifetime;
        $this->stripe_monthly_price_id = $plan->stripe_monthly_price_id;
        $this->stripe_yearly_price_id = $plan->stripe_yearly_price_id;
        $this->stripe_lifetime_price_id = $plan->stripe_lifetime_price_id;
        $this->max_estimations = $plan->max_estimations;
        $this->max_blocks = $plan->max_blocks;
        $this->has_white_label_pdf = (bool) $plan->has_white_label_pdf;
        $this->has_translation_module = (bool) $plan->has_translation_module;
        $this->is_active = (bool) $plan->is_active;

        $this->showForm = true;
    }

    public function delete($id)
    {
        Plan::findOrFail($id)->delete();
        $this->loadPlans();
        session()->flash('message', 'Plan supprimé avec succès.');
    }

    public function render()
    {
        return view('livewire.admin.plan-manager');
    }
}
