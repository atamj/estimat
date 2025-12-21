<div class="space-y-6">
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">{{ $editingSetupId ? 'Modifier' : 'Ajouter' }} une Base Technique</h2>
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Nom de la base</label>
                    <input type="text" wire:model="type" placeholder="ex: Nouveau projet" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                    @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Technologie</label>
                    <select wire:model="project_type_id" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                        <option value="">Générique (aucune)</option>
                        @foreach($projectTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-md border border-blue-200 text-sm text-blue-800 mb-6">
                <p class="mb-2"><strong>Qu'est-ce qu'une Base Technique ?</strong></p>
                <p class="mb-4">Il s'agit du socle de départ de votre projet. Elle comprend l'installation de l'environnement, la configuration initiale des outils et la mise en place de la structure de base nécessaire avant de commencer le développement des fonctionnalités spécifiques.</p>
                <div class="flex items-center">
                    <x-fas-info-circle class="w-4 h-4 inline-block mr-1" />
                    <span>Le système choisira automatiquement entre le <strong>prix forfaitaire</strong> ou le <strong>nombre d'heures</strong> selon le type d'estimation sélectionné (Forfait ou À l'heure) lors de l'estimation.</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Prix Forfaitaire (€)</label>
                    <div class="relative mt-1">
                        <input type="number" step="0.01" wire:model="fixed_price" class="block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 pl-7">
                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-gray-400 font-bold">€</div>
                    </div>
                    @error('fixed_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Nombre d'heures (h)</label>
                    <div class="relative mt-1">
                        <input type="number" step="0.01" wire:model="fixed_hours" class="block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 pr-7">
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-gray-400 font-bold">h</div>
                    </div>
                    @error('fixed_hours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                @if($editingSetupId)
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                    <x-fas-save class="w-4 h-4 mr-2" />
                    {{ $editingSetupId ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom de la base</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technologie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix / Heures</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach($setups as $setup)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $setup->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($setup->projectType)
                                <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-700 rounded flex items-center w-fit">
                                    @if($setup->projectType->icon)
                                        <x-dynamic-component :component="$setup->projectType->icon" class="w-3 h-3 mr-1" />
                                    @endif
                                    {{ $setup->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded text-center">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold">{{ $setup->fixed_price }} €</span> /
                            <span class="font-semibold">{{ $setup->fixed_hours }} h</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $setup->id }})" class="text-blue-600 hover:text-blue-900 mr-2 inline-flex items-center">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                Modifier
                            </button>
                            <button wire:click="duplicate({{ $setup->id }})" class="text-gray-600 hover:text-gray-900 mr-2 inline-flex items-center">
                                <x-fas-copy class="w-4 h-4 mr-1" />
                                Dupliquer
                            </button>
                            <button wire:click="delete({{ $setup->id }})" onclick="confirm('Supprimer cette base technique ?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900 inline-flex items-center">
                                <x-fas-trash class="w-4 h-4 mr-1" />
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
