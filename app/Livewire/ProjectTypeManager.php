<?php

namespace App\Livewire;

use App\Models\ProjectType;
use Livewire\Component;

class ProjectTypeManager extends Component
{
    public $projectTypes;
    public $name, $description, $icon;
    public $editingProjectTypeId = null;

    public $availableIcons = [];

    protected $rules = [
        'name' => 'required|min:2',
        'description' => 'nullable',
        'icon' => 'nullable|string',
    ];

    public function mount()
    {
        $this->availableIcons = ProjectType::getAvailableIcons();
        $this->loadProjectTypes();
    }

    public function loadProjectTypes()
    {
        $this->projectTypes = ProjectType::all();
    }

    public function save()
    {
        $this->validate();

        if ($this->editingProjectTypeId) {
            ProjectType::find($this->editingProjectTypeId)->update([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon ?: null,
            ]);
            session()->flash('message', 'Type de projet mis à jour.');
        } else {
            ProjectType::create([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon ?: null,
            ]);
            session()->flash('message', 'Type de projet créé.');
        }

        $this->resetFields();
        $this->loadProjectTypes();
    }

    public function edit($id)
    {
        $pt = ProjectType::find($id);
        $this->editingProjectTypeId = $id;
        $this->name = $pt->name;
        $this->description = $pt->description;
        $this->icon = $pt->icon;
    }

    public function delete($id)
    {
        ProjectType::find($id)->delete();
        $this->loadProjectTypes();
        session()->flash('message', 'Type de projet supprimé.');
    }

    public function resetFields()
    {
        $this->reset(['name', 'description', 'icon', 'editingProjectTypeId']);
    }

    public function render()
    {
        return view('livewire.project-type-manager');
    }
}
