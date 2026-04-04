<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Gestion des Add-ons</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouvel Add-on
            </button>
        @endif
    </div>

    @if($showForm)
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600 transition-all">
            <h2 class="text-xl font-semibold mb-4 text-blue-800 dark:text-blue-400">{{ $editingOptionId ? 'Modifier' : 'Ajouter' }} un Add-on</h2>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold">Nom</label>
                <input type="text" wire:model="name" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-bold">Type</label>
                <select wire:model.live="type" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="fixed_price">Prix Forfaitaire</option>
                    <option value="fixed_hours">Nombre d'heures</option>
                    <option value="percentage">Pourcentage</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold">Valeur</label>
                <input type="number" step="0.01" wire:model="value" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-bold">Type de Projet</label>
                <select wire:model="project_type_id" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">Générique (aucun)</option>
                    @foreach($projectTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($type == 'percentage')
                <div>
                    <label class="block text-sm font-bold">Base de calcul</label>
                    <select wire:model="calculation_base" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="global">Total Global</option>
                        <option value="blocks">Total Blocs (Prog+Inté)</option>
                        <option value="pages">Total Pages (Instances)</option>
                        <option value="content">Total Contenu uniquement</option>
                        <option value="content_fields">Contenu + Champs</option>
                    </select>
                </div>
            @endif
            <div class="md:col-span-2">
                <label class="block text-sm font-bold">Description</label>
                <textarea wire:model="description" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
            </div>
            <div class="md:col-span-2 flex justify-end gap-2">
                @if($editingOptionId)
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                @else
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Fermer
                    </button>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                    <x-fas-save class="w-4 h-4 mr-2" />
                    {{ $editingOptionId ? 'Mettre à jour' : 'Ajouter' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type Projet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type / Valeur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($options as $option)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $option->name }}</div>
                            <div class="text-xs text-gray-500">{{ $option->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($option->projectType)
                                <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-700 rounded flex items-center w-fit">
                                    @if($option->projectType->icon)
                                        <x-dynamic-component :component="$option->projectType->icon" class="w-3 h-3 mr-1" />
                                    @endif
                                    {{ $option->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-500 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($option->type == 'fixed_price')
                                Prix Forfaitaire : {{ $option->value }} €
                            @elseif($option->type == 'fixed_hours')
                                Heures : {{ $option->value }} h
                            @else
                                Pourcentage : {{ $option->value }} %
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $option->calculation_base ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $option->id }})" class="text-blue-600 hover:text-blue-900 mr-2 inline-flex items-center">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                Modifier
                            </button>
                            <button wire:click="duplicate({{ $option->id }})" class="text-gray-600 hover:text-gray-900 mr-2 inline-flex items-center">
                                <x-fas-copy class="w-4 h-4 mr-1" />
                                Dupliquer
                            </button>
                            <button wire:click="delete({{ $option->id }})" onclick="confirm('Supprimer cet add-on ?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900 inline-flex items-center">
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
