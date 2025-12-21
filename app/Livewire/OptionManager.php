<?php

namespace App\Livewire;

use App\Models\Option;
use App\Models\ProjectType;
use Livewire\Component;

class OptionManager extends Component
{
    public $options;
    public $name, $description, $type = 'fixed_price', $value = 0, $calculation_base = 'global';
    public $project_type_id = null;
    public $editingOptionId = null;

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
        ];

        if ($this->editingOptionId) {
            Option::find($this->editingOptionId)->update($data);
        } else {
            Option::create($data);
        }

        $this->reset(['name', 'description', 'type', 'value', 'calculation_base', 'editingOptionId', 'project_type_id']);
        $this->type = 'fixed_price';
        $this->loadOptions();
    }

    public function edit($id)
    {
        $option = Option::find($id);
        $this->editingOptionId = $id;
        $this->name = $option->name;
        $this->description = $option->description;
        $this->type = $option->type;
        $this->value = $option->value;
        $this->calculation_base = $option->calculation_base ?? 'global';
        $this->project_type_id = $option->project_type_id;
    }

    public function delete($id)
    {
        Option::find($id)->delete();
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
            'projectTypes' => ProjectType::all()
        ]);
    }
}
