<?php

namespace App\Livewire;

use App\Models\Setup;
use App\Models\ProjectType;
use Livewire\Component;

class SetupManager extends Component
{
    public $setups;
    public $type, $fixed_price = 0, $fixed_hours = 0, $project_type_id = null;
    public $editingSetupId = null;
    public $showForm = false;

    protected $rules = [
        'type' => 'required|string|max:255',
        'fixed_price' => 'required|numeric|min:0',
        'fixed_hours' => 'required|numeric|min:0',
        'project_type_id' => 'nullable|exists:project_types,id',
    ];

    public function mount()
    {
        $this->loadSetups();
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
    }

    public function loadSetups()
    {
        $this->setups = Setup::where('user_id', auth()->id())->with('projectType')->get();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'fixed_price' => $this->fixed_price,
            'fixed_hours' => $this->fixed_hours,
            'project_type_id' => $this->project_type_id ?: null,
            'user_id' => auth()->id(),
        ];

        if ($this->editingSetupId) {
            Setup::where('user_id', auth()->id())->findOrFail($this->editingSetupId)->update($data);
        } else {
            Setup::create($data);
        }

        $this->resetFields();
        $this->loadSetups();
    }

    public function edit($id)
    {
        $setup = Setup::where('user_id', auth()->id())->findOrFail($id);
        $this->editingSetupId = $id;
        $this->type = $setup->type;
        $this->fixed_price = $setup->fixed_price;
        $this->fixed_hours = $setup->fixed_hours;
        $this->project_type_id = $setup->project_type_id;
        $this->showForm = true;
    }

    public function delete($id)
    {
        Setup::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->loadSetups();
    }

    public function duplicate($id)
    {
        $setup = Setup::where('user_id', auth()->id())->findOrFail($id);
        $newSetup = $setup->replicate();
        $newSetup->type .= ' (Copie)';
        $newSetup->save();

        $this->loadSetups();
        session()->flash('message', 'Base technique dupliquée avec succès.');
    }

    public function resetFields()
    {
        $this->reset(['type', 'fixed_price', 'fixed_hours', 'project_type_id', 'editingSetupId', 'showForm']);
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
    }

    public function render()
    {
        return view('livewire.setup-manager', [
            'projectTypes' => ProjectType::where('user_id', auth()->id())->get()
        ]);
    }
}
