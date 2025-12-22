<?php

namespace App\Livewire;

use App\Models\TranslationConfig;
use App\Models\ProjectType;
use Livewire\Component;

class TranslationConfigManager extends Component
{
    public $configs;
    public $default_fixed_price = 0;
    public $default_fixed_hours = 0;
    public $default_percentage = 0;
    public $project_type_id = null;
    public $editingConfigId = null;
    public $showForm = false;

    protected $rules = [
        'default_fixed_price' => 'required|numeric|min:0',
        'default_fixed_hours' => 'required|numeric|min:0',
        'default_percentage' => 'required|numeric|min:0',
        'project_type_id' => 'nullable|unique:translation_configs,project_type_id',
    ];

    public function mount()
    {
        $this->loadConfigs();
        $this->project_type_id = ProjectType::where('is_default', true)->value('id');
    }

    public function loadConfigs()
    {
        $this->configs = TranslationConfig::where('user_id', auth()->id())->with('projectType')->get();
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingConfigId) {
            $rules['project_type_id'] = 'nullable|unique:translation_configs,project_type_id,' . $this->editingConfigId . ',id,user_id,' . auth()->id();
        } else {
            $rules['project_type_id'] = 'nullable|unique:translation_configs,project_type_id,NULL,id,user_id,' . auth()->id();
        }
        $this->validate($rules);

        TranslationConfig::updateOrCreate(
            ['id' => $this->editingConfigId, 'user_id' => auth()->id()],
            [
                'default_fixed_price' => $this->default_fixed_price,
                'default_fixed_hours' => $this->default_fixed_hours,
                'default_percentage' => $this->default_percentage,
                'project_type_id' => $this->project_type_id ?: null,
                'user_id' => auth()->id(),
            ]
        );

        $this->resetFields();
        $this->loadConfigs();
        session()->flash('message', 'Configuration de traduction mise à jour.');
    }

    public function edit($id)
    {
        $config = TranslationConfig::where('user_id', auth()->id())->findOrFail($id);
        $this->editingConfigId = $id;
        $this->default_fixed_price = $config->default_fixed_price;
        $this->default_fixed_hours = $config->default_fixed_hours;
        $this->default_percentage = $config->default_percentage;
        $this->project_type_id = $config->project_type_id;
        $this->showForm = true;
    }

    public function delete($id)
    {
        TranslationConfig::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->loadConfigs();
        session()->flash('message', 'Configuration supprimée.');
    }

    public function resetFields()
    {
        $this->reset(['default_fixed_price', 'default_fixed_hours', 'default_percentage', 'project_type_id', 'editingConfigId', 'showForm']);
        $this->project_type_id = ProjectType::where('user_id', auth()->id())->where('is_default', true)->value('id');
    }

    public function render()
    {
        return view('livewire.translation-config-manager', [
            'projectTypes' => ProjectType::where('user_id', auth()->id())->get()
        ]);
    }
}
