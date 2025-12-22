<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $editingUserId = null;
    public $role_admin = false;
    public $selectedPlanId = null;

    protected $queryString = ['search'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->role_admin = (bool) $user->is_admin;
        $this->selectedPlanId = $user->activePlan ? $user->activePlan->id : null;
    }

    public function cancelEdit()
    {
        $this->reset(['editingUserId', 'role_admin', 'selectedPlanId']);
    }

    public function updateUser()
    {
        $user = User::findOrFail($this->editingUserId);

        // Sécurité : Ne pas se retirer son propre rôle admin par erreur ici si on veut,
        // mais l'admin doit pouvoir gérer les autres.

        $user->update([
            'is_admin' => $this->role_admin
        ]);

        // Gestion manuelle de l'abonnement si changé
        if ($this->selectedPlanId) {
            $currentPlan = $user->activePlan;
            if (!$currentPlan || $currentPlan->id != $this->selectedPlanId) {
                // On annule l'ancien et on crée un nouveau
                $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled', 'cancelled_at' => now()]);

                $plan = Plan::find($this->selectedPlanId);
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'type' => $plan->slug === 'pioneer' ? 'lifetime' : 'monthly',
                    'status' => 'active',
                    'starts_at' => now(),
                ]);
            }
        }

        $this->cancelEdit();
        session()->flash('message', 'Utilisateur mis à jour avec succès.');
    }

    public function deleteUser($userId)
    {
        if ($userId == auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }

        User::findOrFail($userId)->delete();
        session()->flash('message', 'Utilisateur supprimé.');
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'plans' => Plan::all()
        ]);
    }
}
