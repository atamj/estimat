<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-6 text-blue-800 border-b pb-2">Configuration de la Traduction par défaut</h2>

        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700">Type de Projet</label>
                <select wire:model="project_type_id" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                    <option value="">Générique (par défaut)</option>
                    @foreach($projectTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                    @endforeach
                </select>
                @error('project_type_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="bg-blue-50 p-4 rounded-md border border-blue-200 text-sm text-blue-800 mb-6">
                <x-fas-info-circle class="w-4 h-4 inline-block mr-1" />
                Le système choisira automatiquement entre le <strong>prix forfaitaire</strong> ou le <strong>nombre d'heures</strong> selon le type d'estimation sélectionné (Forfait ou À l'heure) lors de l'estimation.
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Prix Forfaitaire (€)</label>
                    <input type="number" step="0.01" wire:model="default_fixed_price" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                    @error('default_fixed_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700">Nombre d'heures (h)</label>
                    <input type="number" step="0.01" wire:model="default_fixed_hours" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                    @error('default_fixed_hours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700">Pourcentage par défaut (%) (sur le contenu et la gestion de champs)</label>
                <input type="number" step="0.01" wire:model="default_percentage" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2">
                @error('default_percentage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-2">
                @if($editingConfigId)
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 font-bold transition duration-200 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                @endif
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-bold transition duration-200 flex items-center">
                    <x-fas-save class="w-4 h-4 mr-2" />
                    {{ $editingConfigId ? 'Mettre à jour' : 'Enregistrer les réglages' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des configurations -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type Projet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base / %</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($configs as $config)
                    <tr>
                        <td class="px-6 py-4">
                            @if($config->projectType)
                                <span class="font-bold text-blue-700">{{ $config->projectType->name }}</span>
                            @else
                                <span class="italic text-gray-500">Générique (par défaut)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold">{{ $config->default_fixed_price }} €</span> /
                            <span class="font-semibold">{{ $config->default_fixed_hours }} h</span> /
                            <span class="font-semibold">{{ $config->default_percentage }} %</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $config->id }})" class="text-blue-600 hover:text-blue-900 mr-2 inline-flex items-center">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                Modifier
                            </button>
                            @if($config->project_type_id)
                                <button wire:click="delete({{ $config->id }})" class="text-red-600 hover:text-red-900 inline-flex items-center">
                                    <x-fas-trash class="w-4 h-4 mr-1" />
                                    Supprimer
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
