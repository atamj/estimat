<?php

namespace App\Livewire;

use App\Models\Estimation;
use Livewire\Component;

class EstimationList extends Component
{
    public $search = '';

    public function render()
    {
        $estimations = Estimation::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('client_name', 'like', '%'.$this->search.'%')
                        ->orWhere('project_name', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.estimation-list', [
            'estimations' => $estimations,
        ]);
    }

    public function delete($id)
    {
        $estimation = Estimation::findOrFail($id);
        $estimation->delete();
        session()->flash('message', 'Estimation supprimée avec succès.');
    }

    public function duplicate($id)
    {
        // On redirige vers le contrôleur car la logique de duplication est déjà complexe là-bas
        return redirect()->route('estimations.duplicate', $id);
    }
}
