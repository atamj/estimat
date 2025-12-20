<?php

namespace App\Livewire;

use App\Models\Block;
use App\Models\ProjectType;
use Livewire\Component;

class BlockManager extends Component
{
    public $blocks;
    public $name, $description, $type_unit = 'hour';
    public $price_programming = 0, $price_integration = 0, $price_field_creation = 0, $price_content_management = 0;
    public $project_type_id = null;
    public $filter_project_type = '';
    public $editingBlockId = null;

    protected $rules = [
        'name' => 'required|min:3',
        'type_unit' => 'required|in:hour,fixed',
        'price_programming' => 'required|numeric|min:0',
        'price_integration' => 'required|numeric|min:0',
        'price_field_creation' => 'required|numeric|min:0',
        'price_content_management' => 'required|numeric|min:0',
        'project_type_id' => 'nullable|exists:project_types,id',
    ];

    public function mount()
    {
        $this->loadBlocks();
    }

    public function loadBlocks()
    {
        $query = Block::query();
        if ($this->filter_project_type !== '') {
            if ($this->filter_project_type === 'null') {
                $query->whereNull('project_type_id');
            } else {
                $query->where('project_type_id', $this->filter_project_type);
            }
        }
        $this->blocks = $query->with('projectType')->get();
    }

    public function updatedFilterProjectType()
    {
        $this->loadBlocks();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'type_unit' => $this->type_unit,
            'price_programming' => $this->price_programming,
            'price_integration' => $this->price_integration,
            'price_field_creation' => $this->price_field_creation,
            'price_content_management' => $this->price_content_management,
            'project_type_id' => $this->project_type_id ?: null,
        ];

        if ($this->editingBlockId) {
            Block::find($this->editingBlockId)->update($data);
        } else {
            Block::create($data);
        }

        $this->resetFields();
        $this->loadBlocks();
        session()->flash('message', $this->editingBlockId ? 'Bloc mis à jour.' : 'Bloc créé.');
    }

    public function edit($id)
    {
        $block = Block::findOrFail($id);
        $this->editingBlockId = $id;
        $this->name = $block->name;
        $this->description = $block->description;
        $this->type_unit = $block->type_unit;
        $this->price_programming = $block->price_programming;
        $this->price_integration = $block->price_integration;
        $this->price_field_creation = $block->price_field_creation;
        $this->price_content_management = $block->price_content_management;
        $this->project_type_id = $block->project_type_id;
    }

    public function delete($id)
    {
        Block::find($id)->delete();
        $this->loadBlocks();
    }

    public function resetFields()
    {
        $this->reset(['name', 'description', 'type_unit', 'price_programming', 'price_integration', 'price_field_creation', 'price_content_management', 'editingBlockId', 'project_type_id']);
        $this->type_unit = 'hour';
    }

    public function render()
    {
        return view('livewire.block-manager', [
            'projectTypes' => ProjectType::all()
        ]);
    }
}
