<div class="space-y-8">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-end">
        <button wire:click="toggleForm" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-200">
            <x-fas-plus class="w-4 h-4" />
            {{ $showForm ? 'Annuler' : 'Nouveau Plan' }}
        </button>
    </div>

    @if($showForm)
        <div wire:key="plan-form-{{ $editingPlanId ?? 'new' }}" class="bg-white p-6 rounded-2xl border-2 border-blue-100 shadow-sm animate-fadeIn">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <x-fas-edit class="w-5 h-5 text-blue-600" />
                {{ $editingPlanId ? 'Modifier le Plan' : 'Créer un nouveau Plan' }}
            </h2>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nom du Plan</label>
                        <input type="text" wire:model.live="name" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition" placeholder="Ex: Pro Indépendant">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Slug (URL)</label>
                        <input type="text" wire:model="slug" class="w-full p-2 border-2 border-gray-300 rounded-lg bg-gray-50 outline-none" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                    <textarea wire:model="description" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition" rows="2"></textarea>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Prix Mensuel (€)</label>
                        <input type="number" step="0.01" wire:model="price_monthly" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Prix Annuel (€)</label>
                        <input type="number" step="0.01" wire:model="price_yearly" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Prix Lifetime (€)</label>
                        <input type="number" step="0.01" wire:model="price_lifetime" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition" placeholder="Optionnel">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 p-4 bg-slate-50 rounded-xl">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Estimations Max (-1 = illimité)</label>
                        <input type="number" wire:model="max_estimations" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Blocs Max (-1 = illimité)</label>
                        <input type="number" wire:model="max_blocks" class="w-full p-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 border-t pt-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="has_white_label_pdf" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition">Export PDF Marque Blanche</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="has_translation_module" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition">Module Traduction</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition">Plan Actif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" wire:click="toggleForm" class="px-6 py-2 border-2 border-gray-200 rounded-xl text-slate-600 font-bold hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        {{ $editingPlanId ? 'Mettre à jour' : 'Enregistrer le Plan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-2xl border-2 {{ $plan->is_active ? 'border-slate-200' : 'border-red-100 bg-red-50/30' }} overflow-hidden hover:shadow-lg transition duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
                            <span class="text-xs font-mono text-slate-400 uppercase tracking-tighter">{{ $plan->slug }}</span>
                        </div>
                        @if(!$plan->is_active)
                            <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">Inactif</span>
                        @endif
                    </div>

                    <div class="space-y-2 mb-6">
                        <p class="text-2xl font-black text-slate-900">{{ number_format($plan->price_monthly, 0) }}€ <span class="text-sm font-normal text-slate-500">/mois</span></p>
                        <p class="text-sm text-slate-500">{{ number_format($plan->price_yearly, 0) }}€ /an</p>
                    </div>

                    <div class="space-y-3 py-4 border-t border-slate-100 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Estimations</span>
                            <span class="font-bold text-slate-700">{{ $plan->max_estimations == -1 ? 'Illimitées' : $plan->max_estimations }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">PDF Marque Blanche</span>
                            <span class="font-bold {{ $plan->has_white_label_pdf ? 'text-green-600' : 'text-red-400' }}">
                                {{ $plan->has_white_label_pdf ? 'Oui' : 'Non' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <button wire:click="edit({{ $plan->id }})" class="flex-1 bg-slate-100 text-slate-600 py-2 rounded-lg font-bold hover:bg-slate-200 transition flex items-center justify-center gap-2">
                            <x-fas-edit class="w-4 h-4" /> Modifier
                        </button>
                        <button onclick="confirm('Supprimer ce plan ?') || event.stopImmediatePropagation()" wire:click="delete({{ $plan->id }})" class="bg-red-50 text-red-500 p-2 rounded-lg hover:bg-red-100 transition">
                            <x-fas-trash-alt class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
