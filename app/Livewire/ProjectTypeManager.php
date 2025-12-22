<?php

namespace App\Livewire;

use App\Models\ProjectType;
use Livewire\Component;

class ProjectTypeManager extends Component
{
    public $projectTypes;
    public $name, $description, $icon;
    public $editingProjectTypeId = null;
    public $showForm = false;

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
        $this->projectTypes = ProjectType::where('user_id', auth()->id())->get();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon ?: null,
            'user_id' => auth()->id(),
        ];

        if ($this->editingProjectTypeId) {
            ProjectType::where('user_id', auth()->id())->findOrFail($this->editingProjectTypeId)->update($data);
            session()->flash('message', 'Type de projet mis à jour.');
        } else {
            ProjectType::create($data);
            session()->flash('message', 'Type de projet créé.');
        }

        $this->resetFields();
        $this->loadProjectTypes();
    }

    public function edit($id)
    {
        $pt = ProjectType::where('user_id', auth()->id())->findOrFail($id);
        $this->editingProjectTypeId = $id;
        $this->name = $pt->name;
        $this->description = $pt->description;
        $this->icon = $pt->icon;
        $this->showForm = true;
    }

    public function delete($id)
    {
        ProjectType::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->loadProjectTypes();
        session()->flash('message', 'Type de projet supprimé.');
    }

    public function resetFields()
    {
        $this->reset(['name', 'description', 'icon', 'editingProjectTypeId', 'showForm']);
    }

    public function setAsDefault($id)
    {
        $pt = ProjectType::where('user_id', auth()->id())->findOrFail($id);
        $pt->update(['is_default' => true]);
        $this->loadProjectTypes();
        session()->flash('message', "{$pt->name} est désormais le type de projet par défaut.");
    }

    public function render()
    {
        return view('livewire.project-type-manager', [
            'availableIcons' => ProjectType::getAvailableIcons()
        ]);
    }
}
