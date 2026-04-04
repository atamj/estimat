<div class="space-y-6">

    {{-- Explication --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-4">
        <x-fas-lightbulb class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
        <div class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
            <p class="font-bold">À quoi servent les Add-ons ?</p>
            <p>Ce sont des <strong>options supplémentaires</strong> que vous pouvez ajouter à une estimation : maintenance, hébergement, traduction, référencement, etc. Ils s'ajoutent au prix final.</p>
            <p>Trois types disponibles : <strong>Prix Forfaitaire</strong> (montant fixe en €), <strong>Nombre d'heures</strong> (multiplié par votre taux horaire), ou <strong>Pourcentage</strong> (calculé sur une base du devis).</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes Add-ons</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouvel Add-on
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-t-4 border-blue-600">
            <h2 class="text-xl font-semibold mb-6 text-blue-800 dark:text-blue-400">
                {{ $editingOptionId ? 'Modifier l\'add-on' : 'Créer un nouvel add-on' }}
            </h2>
            <form wire:submit.prevent="save" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="ex: Maintenance annuelle, Hébergement..."
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Technologie</label>
                        <select wire:model="project_type_id"
                                class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            <option value="">Générique (toutes technologies)</option>
                            @foreach($projectTypes as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-4 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Type de tarification <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="type"
                                class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            <option value="fixed_price">Prix Forfaitaire (€ fixe)</option>
                            <option value="fixed_hours">Nombre d'heures (× taux horaire)</option>
                            <option value="percentage">Pourcentage (% du devis)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Valeur
                            <span class="text-xs font-normal text-gray-400 dark:text-gray-500">
                                @if($type === 'fixed_price') (en €)
                                @elseif($type === 'fixed_hours') (en heures)
                                @else (en %)
                                @endif
                            </span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="value" placeholder="0"
                                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 {{ $type === 'fixed_price' ? 'pl-7' : 'pr-7' }} bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            @if($type === 'fixed_price')
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400 font-bold pointer-events-none">€</span>
                            @elseif($type === 'fixed_hours')
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 font-bold pointer-events-none">h</span>
                            @else
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 font-bold pointer-events-none">%</span>
                            @endif
                        </div>
                        @error('value') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    @if($type === 'percentage')
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Base de calcul du pourcentage</label>
                            <select wire:model="calculation_base"
                                    class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                                <option value="global">Total Global du devis</option>
                                <option value="blocks">Total Blocs (Programmation + Intégration)</option>
                                <option value="pages">Total Pages (Instances)</option>
                                <option value="content">Total Contenu uniquement</option>
                                <option value="content_fields">Contenu + Gestion de Champs</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Description <span class="text-gray-400 font-normal">(optionnel)</span></label>
                    <textarea wire:model="description" rows="2" placeholder="Décrivez cet add-on pour vous souvenir à quoi il sert..."
                              class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="resetFields"
                            class="px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold flex items-center gap-2 transition-colors shadow-sm">
                        <x-fas-save class="w-4 h-4" />
                        {{ $editingOptionId ? 'Mettre à jour' : 'Créer l\'add-on' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Technologie</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type / Valeur</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Base %</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($options as $option)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $option->name }}</div>
                            @if($option->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $option->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($option->projectType)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded">
                                    @if($option->projectType->icon)
                                        <x-dynamic-component :component="$option->projectType->icon" class="w-3 h-3" />
                                    @endif
                                    {{ $option->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            @if($option->type === 'fixed_price')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded text-xs font-bold">
                                    {{ number_format($option->value, 2) }} €
                                    <span class="font-normal text-emerald-500">forfait</span>
                                </span>
                            @elseif($option->type === 'fixed_hours')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded text-xs font-bold">
                                    {{ number_format($option->value, 1) }} h
                                    <span class="font-normal text-amber-500">× taux</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded text-xs font-bold">
                                    {{ number_format($option->value, 1) }} %
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            @if($option->type === 'percentage' && $option->calculation_base)
                                @php
                                    $bases = [
                                        'global' => 'Total global',
                                        'blocks' => 'Prog + Inté',
                                        'pages' => 'Pages',
                                        'content' => 'Contenu',
                                        'content_fields' => 'Contenu + Champs',
                                    ];
                                @endphp
                                {{ $bases[$option->calculation_base] ?? $option->calculation_base }}
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-3">
                                <button wire:click="edit({{ $option->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center gap-1 text-sm font-medium transition-colors">
                                    <x-fas-edit class="w-4 h-4" />
                                    Modifier
                                </button>
                                <button wire:click="duplicate({{ $option->id }})" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 inline-flex items-center gap-1 text-sm transition-colors">
                                    <x-fas-copy class="w-4 h-4" />
                                    Dupliquer
                                </button>
                                <button wire:click="delete({{ $option->id }})" onclick="confirm('Supprimer cet add-on ?') || event.stopImmediatePropagation()"
                                        class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 inline-flex items-center gap-1 text-sm transition-colors">
                                    <x-fas-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-fas-puzzle-piece class="w-8 h-8 text-blue-400" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 font-bold text-lg mb-1">Aucun add-on</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6 max-w-sm mx-auto">Créez vos premiers add-ons (maintenance, hébergement, SEO…) pour les proposer facilement dans vos estimations.</p>
                            <button wire:click="$set('showForm', true)" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                <x-fas-plus class="w-4 h-4" />
                                Créer mon premier add-on
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
