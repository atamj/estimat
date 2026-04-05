<?php

namespace App\Livewire;

use App\Models\Option;
use App\Models\ProjectType;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OptionManager extends Component
{
    public $options;

    public $name;

    public $description;

    public $type = 'fixed_price';

    public $value = 0;

    public $calculation_base = 'global';

    public $project_type_id = null;

    public $editingOptionId = null;

    public $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:fixed_price,fixed_hours,percentage',
        'value' => 'required|numeric|min:0',
        'calculation_base' => 'required|string',
        'project_type_id' => 'nullable|exists:project_types,id',
    ];

    public function mount()
    {
        $this->loadOptions();
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
    }

    public function loadOptions()
    {
        $this->options = Option::with('projectType')->get();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'value' => $this->value,
            'calculation_base' => $this->type === 'percentage' ? $this->calculation_base : null,
            'project_type_id' => $this->project_type_id ?: null,
            'user_id' => Auth::id(),
        ];

        if ($this->editingOptionId) {
            Option::findOrFail($this->editingOptionId)->update($data);
        } else {
            Option::create($data);
        }

        $this->resetFields();
        $this->loadOptions();
    }

    public function resetFields()
    {
        $this->reset(['name', 'description', 'type', 'value', 'calculation_base', 'editingOptionId', 'project_type_id', 'showForm']);
        $this->type = 'fixed_price';
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
    }

    public function edit($id)
    {
        $option = Option::findOrFail($id);
        $this->editingOptionId = $id;
        $this->name = $option->name;
        $this->description = $option->description;
        $this->type = $option->type;
        $this->value = $option->value;
        $this->calculation_base = $option->calculation_base ?? 'global';
        $this->project_type_id = $option->project_type_id;
        $this->showForm = true;
    }

    public function delete($id)
    {
        Option::findOrFail($id)->delete();
        $this->loadOptions();
    }

    public function duplicate($id)
    {
        $option = Option::findOrFail($id);
        $newOption = $option->replicate();
        $newOption->name .= ' (Copy)';
        $newOption->save();

        $this->loadOptions();
        session()->flash('message', 'Option duplicated successfully.');
    }

    public function render()
    {
        return view('livewire.option-manager', [
            'projectTypes' => ProjectType::all(),
        ]);
    }
}
