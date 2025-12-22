<div class="space-y-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Catalogue de Blocs</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouveau Bloc
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Formulaire -->
    @if($showForm)
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600 transition-all">
            <h2 class="text-xl font-semibold mb-4 text-blue-800">{{ $editingBlockId ? 'Modifier le bloc' : 'Créer un nouveau bloc' }}</h2>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700">Nom du bloc</label>
                <input type="text" wire:model="name" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-700">Description</label>
                <textarea wire:model="description" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Type de Projet</label>
                <select wire:model="project_type_id" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                    <option value="">Générique (aucun)</option>
                    @foreach($projectTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Type d'unité</label>
                <select wire:model="type_unit" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                    <option value="hour">Heure</option>
                    <option value="fixed">Forfait (Prix)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Programmation (Fixe)</label>
                <input type="number" step="0.01" wire:model="price_programming" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Intégration (Fixe)</label>
                <input type="number" step="0.01" wire:model="price_integration" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Création de champs (Variable)</label>
                <input type="number" step="0.01" wire:model="price_field_creation" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Gestion de contenu (Variable)</label>
                <input type="number" step="0.01" wire:model="price_content_management" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
            </div>

            <div class="col-span-2 flex justify-end space-x-2">
                @if($editingBlockId)
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                @else
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                    <x-fas-save class="w-4 h-4 mr-2" />
                    {{ $editingBlockId ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Liste des blocs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Catalogue</h3>
            <div class="flex items-center space-x-2">
                <label class="text-xs font-bold text-gray-500 uppercase">Filtrer par type :</label>
                <select wire:model.live="filter_project_type" class="text-sm border-2 border-gray-300 rounded p-1">
                    <option value="">Tous les blocs</option>
                    <option value="null">Génériques uniquement</option>
                    @foreach($projectTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type Projet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prog / Inté</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Champs / Contenu</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($blocks as $block)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $block->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($block->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($block->projectType)
                                <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-700 rounded flex items-center w-fit">
                                    @if($block->projectType->icon)
                                        <x-dynamic-component :component="$block->projectType->icon" class="w-3 h-3 mr-1" />
                                    @endif
                                    {{ $block->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $block->type_unit == 'hour' ? 'Heure' : 'Forfait' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $block->price_programming }} / {{ $block->price_integration }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $block->price_field_creation }} / {{ $block->price_content_management }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $block->id }})" class="text-blue-600 hover:text-blue-900 mr-2 inline-flex items-center">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                Modifier
                            </button>
                            <button wire:click="duplicate({{ $block->id }})" class="text-gray-600 hover:text-gray-900 mr-2 inline-flex items-center">
                                <x-fas-copy class="w-4 h-4 mr-1" />
                                Dupliquer
                            </button>
                            <button wire:click="delete({{ $block->id }})" onclick="confirm('Supprimer ce bloc ?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900 inline-flex items-center">
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
