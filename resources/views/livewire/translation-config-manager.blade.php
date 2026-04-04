<div class="space-y-6">

    {{-- Explication --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-4">
        <x-fas-lightbulb class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
        <div class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
            <p class="font-bold">À quoi sert la Traduction ?</p>
            <p>Configurez le <strong>coût de la traduction de contenu</strong> à ajouter automatiquement dans les estimations. Définissez un prix forfaitaire (mode Forfait), un nombre d'heures (mode À l'heure), et/ou un pourcentage appliqué sur le contenu et les champs.</p>
            <p>Vous pouvez définir des tarifs différents <strong>par technologie</strong>, ou un tarif <strong>générique</strong> qui s'applique à toutes.</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Réglages de Traduction</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouvelle Configuration
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
                {{ $editingConfigId ? 'Modifier la configuration' : 'Créer une nouvelle configuration' }}
            </h2>
            <form wire:submit.prevent="save" class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Technologie</label>
                    <select wire:model="project_type_id"
                            class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                        <option value="">Générique (toutes technologies)</option>
                        @foreach($projectTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                    @error('project_type_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-5 p-4 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Prix Forfaitaire
                            <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(mode Forfait)</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="default_fixed_price" placeholder="0"
                                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 pl-7 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400 font-bold pointer-events-none">€</span>
                        </div>
                        @error('default_fixed_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Nombre d'heures
                            <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(mode À l'heure)</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="default_fixed_hours" placeholder="0"
                                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 pr-7 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 font-bold pointer-events-none">h</span>
                        </div>
                        @error('default_fixed_hours') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Pourcentage sur le contenu et les champs
                            <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(optionnel)</span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" wire:model="default_percentage" placeholder="0"
                                   class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 pr-7 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 font-bold pointer-events-none">%</span>
                        </div>
                        @error('default_percentage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="resetFields"
                            class="px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold flex items-center gap-2 transition-colors shadow-sm">
                        <x-fas-save class="w-4 h-4" />
                        {{ $editingConfigId ? 'Mettre à jour' : 'Créer la configuration' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Technologie</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Forfait</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">À l'heure</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">% Contenu</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($configs as $config)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            @if($config->projectType)
                                <div class="flex items-center gap-2">
                                    @if($config->projectType->icon)
                                        <div class="w-7 h-7 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                            <x-dynamic-component :component="$config->projectType->icon" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                    @endif
                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $config->projectType->name }}</span>
                                </div>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ number_format($config->default_fixed_price, 2) }} €
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ number_format($config->default_fixed_hours, 1) }} h
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            {{ number_format($config->default_percentage, 1) }} %
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-3">
                                <button wire:click="edit({{ $config->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center gap-1 text-sm font-medium transition-colors">
                                    <x-fas-edit class="w-4 h-4" />
                                    Modifier
                                </button>
                                @if($config->project_type_id)
                                    <button wire:click="delete({{ $config->id }})" onclick="confirm('Supprimer cette configuration de traduction ?') || event.stopImmediatePropagation()"
                                            class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 inline-flex items-center gap-1 text-sm transition-colors">
                                        <x-fas-trash class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-fas-language class="w-8 h-8 text-blue-400" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 font-bold text-lg mb-1">Aucune configuration de traduction</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6 max-w-sm mx-auto">Configurez le coût de la traduction pour qu'il soit proposé automatiquement dans vos estimations.</p>
                            <button wire:click="$set('showForm', true)" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                <x-fas-plus class="w-4 h-4" />
                                Créer ma première configuration
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
