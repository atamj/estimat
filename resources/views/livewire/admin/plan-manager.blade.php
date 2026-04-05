<div class="space-y-8">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-end">
        <button wire:click="toggleForm" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-200">
            <x-fas-plus class="w-4 h-4" />
            {{ $showForm ? 'Annuler' : 'Nouveau Plan' }}
        </button>
    </div>

    @if($showForm)
        <div wire:key="plan-form-{{ $editingPlanId ?? 'new' }}" class="bg-white dark:bg-gray-800 p-6 rounded-2xl border-2 border-blue-100 dark:border-blue-900 shadow-sm animate-fadeIn">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 dark:text-gray-100">
                <x-fas-edit class="w-5 h-5 text-blue-600" />
                {{ $editingPlanId ? 'Modifier le Plan' : 'Créer un nouveau Plan' }}
            </h2>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Nom du Plan</label>
                        <input type="text" wire:model.live="name" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Ex: Pro Indépendant">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Slug (URL)</label>
                        <input type="text" wire:model="slug" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 outline-none" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea wire:model="description" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" rows="2"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-3">Type de facturation</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border-2 p-4 transition {{ $billing_type === 'recurring' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <input type="radio" wire:model.live="billing_type" value="recurring" class="sr-only">
                            <span class="flex flex-col">
                                <span class="font-bold text-sm text-slate-800 dark:text-gray-100">Récurrent</span>
                                <span class="text-xs text-slate-500 dark:text-gray-400 mt-0.5">Mensuel et/ou annuel</span>
                            </span>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border-2 p-4 transition {{ $billing_type === 'lifetime' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <input type="radio" wire:model.live="billing_type" value="lifetime" class="sr-only">
                            <span class="flex flex-col">
                                <span class="font-bold text-sm text-slate-800 dark:text-gray-100">À vie</span>
                                <span class="text-xs text-slate-500 dark:text-gray-400 mt-0.5">Paiement unique</span>
                            </span>
                        </label>
                    </div>
                </div>

                @if($billing_type === 'recurring')
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Prix Mensuel (€)</label>
                            <input type="number" step="0.01" wire:model="price_monthly" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @error('price_monthly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Prix Annuel (€)</label>
                            <input type="number" step="0.01" wire:model="price_yearly" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            @error('price_yearly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @else
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Prix Lifetime (€)</label>
                        <input type="number" step="0.01" wire:model="price_lifetime" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="ex: 299">
                        @error('price_lifetime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                @endif

                @if($editingPlanId)
                    @php $editingPlan = $plans->firstWhere('id', $editingPlanId); @endphp
                    @if($editingPlan?->stripe_product_id)
                        <div class="p-4 bg-purple-50 dark:bg-purple-900/10 rounded-xl border border-purple-100 dark:border-purple-900 space-y-3">
                            <h3 class="text-sm font-bold text-purple-700 dark:text-purple-300 flex items-center gap-2">
                                <x-fas-check-circle class="w-4 h-4 text-green-500" />
                                Synchronisé avec Stripe
                            </h3>
                            <div class="grid md:grid-cols-3 gap-3 text-xs font-mono text-slate-500 dark:text-gray-400">
                                <div>
                                    <span class="block font-sans font-bold text-slate-600 dark:text-gray-400 mb-0.5">Produit</span>
                                    {{ $editingPlan->stripe_product_id }}
                                </div>
                                @if($editingPlan->stripe_monthly_price_id)
                                    <div>
                                        <span class="block font-sans font-bold text-slate-600 dark:text-gray-400 mb-0.5">Mensuel</span>
                                        {{ $editingPlan->stripe_monthly_price_id }}
                                    </div>
                                @endif
                                @if($editingPlan->stripe_yearly_price_id)
                                    <div>
                                        <span class="block font-sans font-bold text-slate-600 dark:text-gray-400 mb-0.5">Annuel</span>
                                        {{ $editingPlan->stripe_yearly_price_id }}
                                    </div>
                                @endif
                            </div>
                            <p class="text-xs text-purple-600 dark:text-purple-400 italic">Les identifiants Stripe sont mis à jour automatiquement à la sauvegarde.</p>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <x-fas-info-circle class="w-4 h-4 text-gray-400" />
                                La synchronisation Stripe s'effectuera automatiquement à la sauvegarde (nécessite STRIPE_SECRET dans .env).
                            </p>
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            <x-fas-info-circle class="w-4 h-4 text-gray-400" />
                            Le produit et les prix Stripe seront créés automatiquement à la sauvegarde (nécessite STRIPE_SECRET dans .env).
                        </p>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-6 p-4 bg-slate-50 dark:bg-gray-900 rounded-xl">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Estimations Max (-1 = illimité)</label>
                        <input type="number" wire:model="max_estimations" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Blocs Max (-1 = illimité)</label>
                        <input type="number" wire:model="max_blocks" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 border-t pt-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="has_white_label_pdf" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Export PDF Marque Blanche</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="has_translation_module" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Module Traduction</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Plan Actif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" wire:click="toggleForm" class="px-6 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-slate-600 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 {{ $plan->is_active ? 'border-slate-200 dark:border-gray-700' : 'border-red-100 bg-red-50/30 dark:border-red-900 dark:bg-red-900/10' }} overflow-hidden hover:shadow-lg transition duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-gray-100">{{ $plan->name }}</h3>
                            <span class="text-xs font-mono text-slate-400 dark:text-gray-500 uppercase tracking-tighter">{{ $plan->slug }}</span>
                        </div>
                        @if(!$plan->is_active)
                            <span class="bg-red-100 text-red-600 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">Inactif</span>
                        @endif
                    </div>

                    <div class="space-y-2 mb-6">
                        @if($plan->price_lifetime > 0)
                            <p class="text-2xl font-black text-slate-900 dark:text-gray-100">{{ $plan->price_lifetime + 0 }}€ <span class="text-sm font-normal text-slate-500 dark:text-gray-400">à vie</span></p>
                            <p class="text-sm text-slate-500 dark:text-gray-400">Paiement unique</p>
                        @elseif($plan->price_monthly > 0)
                            <p class="text-2xl font-black text-slate-900 dark:text-gray-100">{{ $plan->price_monthly + 0 }}€ <span class="text-sm font-normal text-slate-500 dark:text-gray-400">/mois</span></p>
                            <p class="text-sm text-slate-500 dark:text-gray-400">{{ $plan->price_yearly + 0 }}€ /an</p>
                        @else
                            <p class="text-2xl font-black text-slate-900 dark:text-gray-100">Gratuit</p>
                        @endif
                    </div>

                    <div class="space-y-3 py-4 border-t border-slate-100 dark:border-gray-700 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-gray-400">Estimations</span>
                            <span class="font-bold text-slate-700 dark:text-gray-300">{{ $plan->max_estimations == -1 ? 'Illimitées' : $plan->max_estimations }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-gray-400">PDF Marque Blanche</span>
                            <span class="font-bold {{ $plan->has_white_label_pdf ? 'text-green-600' : 'text-red-400' }}">
                                {{ $plan->has_white_label_pdf ? 'Oui' : 'Non' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500 dark:text-gray-400">Stripe</span>
                            @if($plan->stripe_product_id)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-600">
                                    <x-fas-check-circle class="w-3 h-3" /> Synchronisé
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-400">
                                    <x-fas-minus-circle class="w-3 h-3" /> Non synchronisé
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <button wire:click="edit({{ $plan->id }})" class="flex-1 bg-slate-100 dark:bg-gray-700 text-slate-600 dark:text-gray-300 py-2 rounded-lg font-bold hover:bg-slate-200 dark:hover:bg-gray-600 transition flex items-center justify-center gap-2">
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
