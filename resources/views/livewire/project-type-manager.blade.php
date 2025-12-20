<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">{{ $editingProjectTypeId ? 'Modifier' : 'Ajouter' }} un Type de Projet</h2>
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Nom du type</label>
                <input type="text" wire:model="name" placeholder="ex: WordPress, Laravel..." class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700">Description</label>
                <textarea wire:model="description" class="w-full border-2 border-gray-300 rounded-md p-2 focus:border-blue-500 focus:ring focus:ring-blue-200"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Icône</label>
                <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-12 gap-2">
                    <button type="button" wire:click="$set('icon', null)"
                            class="p-2 rounded border-2 {{ is_null($icon) ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-200' }}"
                            title="Aucune icône">
                        <x-fas-ban class="w-6 h-6 text-gray-400" />
                    </button>
                    @foreach($availableIcons as $iconCode => $iconName)
                        <button type="button" wire:click="$set('icon', '{{ $iconCode }}')"
                                class="p-2 rounded border-2 {{ $icon === $iconCode ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-200' }}"
                                title="{{ $iconName }}">
                            <x-dynamic-component :component="$iconCode" class="w-6 h-6" />
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="md:col-span-2 flex justify-end gap-2">
                @if($editingProjectTypeId)
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                    <x-fas-save class="w-4 h-4 mr-2" />
                    {{ $editingProjectTypeId ? 'Mettre à jour' : 'Enregistrer' }}
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($projectTypes as $type)
                    <tr>
                        <td class="px-6 py-4 font-medium flex items-center">
                            @if($type->icon)
                                <x-dynamic-component :component="$type->icon" class="w-5 h-5 mr-3 text-blue-600" />
                            @else
                                <x-fas-question-circle class="w-5 h-5 mr-3 text-gray-300" />
                            @endif
                            {{ $type->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $type->description }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $type->id }})" class="text-blue-600 hover:text-blue-900 mr-2 inline-flex items-center">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                Modifier
                            </button>
                            <button wire:click="delete({{ $type->id }})" onclick="confirm('Supprimer ce type ?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900 inline-flex items-center">
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
