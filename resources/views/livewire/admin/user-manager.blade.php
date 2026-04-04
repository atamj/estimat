<div class="space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Recherche -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700">
        <div class="relative">
            <x-fas-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-gray-500" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un utilisateur (nom ou email)..."
                   class="w-full pl-10 pr-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
        </div>
    </div>

    @if($editingUserId)
        <!-- Formulaire d'édition rapide -->
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border-2 border-blue-200 dark:border-blue-800 shadow-sm animate-fadeIn">
            <h3 class="text-lg font-bold mb-4 flex items-center gap-2 dark:text-gray-100">
                <x-fas-user-edit class="w-5 h-5 text-blue-600" />
                Modifier l'utilisateur : {{ \App\Models\User::find($editingUserId)->name }}
            </h3>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Rôle</label>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" wire:model="role_admin" id="role_admin" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label for="role_admin" class="text-sm font-medium text-slate-700 dark:text-gray-300">Administrateur</label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Plan / Abonnement</label>
                    <select wire:model="selectedPlanId" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Sélectionner un plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="cancelEdit" class="px-4 py-2 text-slate-600 dark:text-gray-300 font-bold hover:bg-slate-100 dark:hover:bg-gray-700 rounded-lg transition">Annuler</button>
                <button wire:click="updateUser" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition">Enregistrer</button>
            </div>
        </div>
    @endif

    <!-- Liste des utilisateurs -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-gray-900 border-b border-slate-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Utilisateur</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Plan Actif</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider">Date Inscription</th>
                    <th class="px-6 py-4 text-sm font-bold text-slate-700 dark:text-gray-300 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-gray-100">{{ $user->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-gray-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_admin)
                                <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 text-[10px] px-2 py-0.5 rounded-full uppercase font-black">Admin</span>
                            @else
                                <span class="bg-slate-100 dark:bg-gray-700 text-slate-500 dark:text-gray-400 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($user->activePlan)
                                <span class="text-sm font-medium text-slate-700 dark:text-gray-300">{{ $user->activePlan->name }}</span>
                            @else
                                <span class="text-xs italic text-slate-400 dark:text-gray-500">Aucun plan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500 dark:text-gray-400">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="editUser({{ $user->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Modifier">
                                    <x-fas-edit class="w-4 h-4" />
                                </button>
                                <button onclick="confirm('Supprimer cet utilisateur ? Toutes ses données seront perdues.') || event.stopImmediatePropagation()"
                                        wire:click="deleteUser({{ $user->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Supprimer">
                                    <x-fas-trash-alt class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
