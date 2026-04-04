<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Illuminate\Support\Str;
use Livewire\Component;

class PlanManager extends Component
{
    public $plans;

    public $name;

    public $slug;

    public $description;

    public $price_monthly;

    public $price_yearly;

    public $price_lifetime;

    public $max_estimations = -1;

    public $max_blocks = -1;

    public $has_white_label_pdf = false;

    public $has_translation_module = true;

    public $is_active = true;

    public $editingPlanId = null;

    public $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'price_monthly' => 'required|numeric|min:0',
        'price_yearly' => 'required|numeric|min:0',
        'price_lifetime' => 'nullable|numeric|min:0',
    ];

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

    public function resetFields()
    {
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->price_monthly = 0;
        $this->price_yearly = 0;
        $this->price_lifetime = null;
        $this->max_estimations = -1;
        $this->max_blocks = -1;
        $this->has_white_label_pdf = false;
        $this->has_translation_module = true;
        $this->is_active = true;
        $this->editingPlanId = null;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price_monthly' => $this->price_monthly,
            'price_yearly' => $this->price_yearly,
            'price_lifetime' => $this->price_lifetime,
            'max_estimations' => $this->max_estimations,
            'max_blocks' => $this->max_blocks,
            'has_white_label_pdf' => $this->has_white_label_pdf,
            'has_translation_module' => $this->has_translation_module,
            'is_active' => $this->is_active,
        ];

        if ($this->editingPlanId) {
            Plan::findOrFail($this->editingPlanId)->update($data);
            session()->flash('message', 'Plan mis à jour avec succès.');
        } else {
            Plan::create($data);
            session()->flash('message', 'Plan créé avec succès.');
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
        $this->price_monthly = $plan->price_monthly;
        $this->price_yearly = $plan->price_yearly;
        $this->price_lifetime = $plan->price_lifetime;
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
