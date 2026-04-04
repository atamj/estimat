<div class="space-y-6">

    {{-- Explication --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-4">
        <x-fas-lightbulb class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
        <div class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
            <p class="font-bold">À quoi servent les Types de Projet ?</p>
            <p>Ils permettent de <strong>filtrer les blocs et add-ons</strong> disponibles lors de la création d'une estimation. Par exemple : un type "WordPress" n'affichera que les blocs WordPress dans le builder.</p>
            <p>Le type <strong>par défaut</strong> est pré-sélectionné automatiquement à chaque nouvelle estimation.</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes Types de Projet</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouveau Type
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-t-4 border-blue-600">
            <h2 class="text-xl font-semibold mb-6 text-blue-800 dark:text-blue-400">
                {{ $editingProjectTypeId ? 'Modifier le type de projet' : 'Créer un nouveau type de projet' }}
            </h2>
            <form wire:submit.prevent="save" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="ex: WordPress, Laravel, Shopify..."
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Description <span class="text-gray-400 font-normal">(optionnel)</span></label>
                        <input type="text" wire:model="description" placeholder="ex: Sites sous WordPress avec Elementor"
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Icône</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Survolez une icône pour voir son nom, cliquez pour la sélectionner.</p>
                    <div class="grid grid-cols-8 sm:grid-cols-12 md:grid-cols-16 gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 max-h-48 overflow-y-auto">
                        <button type="button" wire:click="$set('icon', null)"
                                class="p-2 rounded-lg border-2 transition-colors {{ is_null($icon) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/40' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}"
                                title="Aucune icône">
                            <x-fas-ban class="w-5 h-5 text-gray-400" />
                        </button>
                        @foreach($availableIcons as $iconCode => $iconName)
                            <button type="button" wire:click="$set('icon', '{{ $iconCode }}')"
                                    class="p-2 rounded-lg border-2 transition-colors {{ $icon === $iconCode ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/40' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}"
                                    title="{{ $iconName }}">
                                <x-dynamic-component :component="$iconCode" class="w-5 h-5 dark:text-gray-300" />
                            </button>
                        @endforeach
                    </div>
                    @if($icon)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            Sélectionnée : <x-dynamic-component :component="$icon" class="w-4 h-4 text-blue-600" />
                            <span class="font-medium text-blue-600">{{ $availableIcons[$icon] ?? $icon }}</span>
                        </p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="resetFields" class="px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold flex items-center gap-2 transition-colors shadow-sm">
                        <x-fas-save class="w-4 h-4" />
                        {{ $editingProjectTypeId ? 'Mettre à jour' : 'Créer le type' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($projectTypes as $type)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($type->icon)
                                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <x-dynamic-component :component="$type->icon" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <x-fas-question class="w-4 h-4 text-gray-400" />
                                    </div>
                                @endif
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $type->name }}</span>
                                    @if($type->is_default)
                                        <span class="ml-2 px-2 py-0.5 text-[10px] font-bold bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 rounded-full">
                                            ★ Défaut
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $type->description ?: '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-3">
                                @if(!$type->is_default)
                                    <button wire:click="setAsDefault({{ $type->id }})"
                                            class="text-xs text-gray-400 dark:text-gray-500 hover:text-green-600 dark:hover:text-green-400 font-medium flex items-center gap-1 transition-colors whitespace-nowrap"
                                            title="Pré-sélectionner ce type à la création d'une estimation">
                                        <x-fas-star class="w-3.5 h-3.5" />
                                        Défaut
                                    </button>
                                @else
                                    <span class="w-[4.5rem]"></span>
                                @endif
                                <button wire:click="edit({{ $type->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center gap-1 text-sm font-medium transition-colors">
                                    <x-fas-edit class="w-4 h-4" />
                                    Modifier
                                </button>
                                <button wire:click="delete({{ $type->id }})" onclick="confirm('Supprimer ce type ? Les blocs associés ne seront pas supprimés.') || event.stopImmediatePropagation()"
                                        class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 inline-flex items-center gap-1 text-sm transition-colors">
                                    <x-fas-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-fas-microchip class="w-8 h-8 text-blue-400" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 font-bold text-lg mb-1">Aucun type de projet</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6 max-w-sm mx-auto">Créez votre premier type (ex: WordPress, Laravel…) pour organiser vos blocs et gagner du temps dans le builder.</p>
                            <button wire:click="$set('showForm', true)" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                <x-fas-plus class="w-4 h-4" />
                                Créer mon premier type
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
