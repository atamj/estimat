<div class="space-y-8">

    {{-- En-tête --}}
    <div class="flex justify-between items-start">
        <p class="text-sm text-gray-500 dark:text-gray-400">Les blocs sont les composants réutilisables de vos estimations. Chaque bloc contient des tarifs par catégorie de travail.</p>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="ml-4 shrink-0 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouveau bloc
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulaire --}}
    @if($showForm)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-t-4 border-blue-600">
            <h2 class="text-lg font-bold mb-4 text-blue-800 dark:text-blue-400">
                {{ $editingBlockId ? 'Modifier le bloc' : 'Créer un nouveau bloc' }}
            </h2>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Nom du bloc</label>
                    <input type="text" wire:model="name" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Description <span class="font-normal text-gray-400">(visible dans le catalogue pour expliquer ce que fait ce bloc)</span></label>
                    <textarea wire:model="description" rows="2" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Type de projet</label>
                    <select wire:model="project_type_id" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">Générique — visible pour tous les types de projets</option>
                        @foreach($projectTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }} uniquement</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Unité de facturation</label>
                    <select wire:model="type_unit" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="hour">À l'heure (h)</option>
                        <option value="fixed">Forfait (€)</option>
                    </select>
                </div>

                {{-- Légende des champs de tarifs --}}
                <div class="col-span-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-xs text-blue-800 dark:text-blue-300 space-y-1">
                    <p class="font-bold text-blue-900 dark:text-blue-200 mb-1">À quoi correspondent ces tarifs ?</p>
                    <p><span class="font-bold">Programmation</span> — temps de développement back-end, logique métier, code. Compté une seule fois par type de bloc dans le devis.</p>
                    <p><span class="font-bold">Intégration</span> — temps d'intégration HTML/CSS, mise en page, animations. Compté une seule fois par type de bloc.</p>
                    <p><span class="font-bold">Création de champs</span> — temps de création des champs de saisie dans le CMS (ACF, Filament…). Multiplié par la quantité.</p>
                    <p><span class="font-bold">Gestion de contenu</span> — temps de saisie et mise en forme du contenu final. Multiplié par la quantité ET le nombre de pages similaires.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Programmation <span class="font-normal text-gray-400">(fixe)</span></label>
                    <input type="number" step="0.01" wire:model="price_programming" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @error('price_programming') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Intégration <span class="font-normal text-gray-400">(fixe)</span></label>
                    <input type="number" step="0.01" wire:model="price_integration" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @error('price_integration') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Création de champs <span class="font-normal text-gray-400">(variable × qté)</span></label>
                    <input type="number" step="0.01" wire:model="price_field_creation" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @error('price_field_creation') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Gestion de contenu <span class="font-normal text-gray-400">(variable × qté × pages)</span></label>
                    <input type="number" step="0.01" wire:model="price_content_management" class="mt-1 block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    @error('price_content_management') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2 flex justify-end gap-2">
                    <button type="button" wire:click="resetFields" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 flex items-center">
                        <x-fas-times class="w-4 h-4 mr-2" />
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
                        <x-fas-save class="w-4 h-4 mr-2" />
                        {{ $editingBlockId ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Liste des blocs --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">

        {{-- Barre de filtre --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700 flex flex-wrap justify-between items-center gap-3">
            <div>
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $blocks->count() }} bloc(s) dans votre catalogue</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Filtrer :</label>
                <select wire:model.live="filter_project_type" class="text-sm border-2 border-gray-300 dark:border-gray-600 rounded p-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Tous les blocs</option>
                    <option value="null">Génériques uniquement</option>
                    @foreach($projectTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }} uniquement</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Légende des colonnes --}}
        <div class="px-4 py-2 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex flex-wrap gap-4">
            <span><span class="font-bold">Prog.</span> = Programmation (fixe par bloc)</span>
            <span><span class="font-bold">Inté.</span> = Intégration (fixe par bloc)</span>
            <span><span class="font-bold">Champs</span> = Création de champs (× quantité)</span>
            <span><span class="font-bold">Contenu</span> = Gestion contenu (× quantité × pages)</span>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bloc & description</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type de projet</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prog. / Inté. <span class="normal-case font-normal">(fixes)</span></th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Champs / Contenu <span class="normal-case font-normal">(variables)</span></th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($blocks as $block)
                    @php $unit = $block->type_unit === 'hour' ? 'h' : '€'; @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $block->name }}</div>
                            @if($block->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs leading-relaxed">{{ $block->description }}</div>
                            @else
                                <div class="text-xs text-gray-300 dark:text-gray-600 mt-1 italic">Aucune description</div>
                            @endif
                            <span class="inline-block mt-1 text-[10px] font-bold px-1.5 py-0.5 rounded {{ $block->type_unit === 'hour' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $block->type_unit === 'hour' ? 'À l\'heure' : 'Forfait' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($block->projectType)
                                <span class="px-2 py-1 text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 rounded flex items-center w-fit gap-1">
                                    @if($block->projectType->icon)
                                        <x-dynamic-component :component="$block->projectType->icon" class="w-3 h-3" />
                                    @endif
                                    {{ $block->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase w-10">Prog.</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $block->price_programming }}{{ $unit }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase w-10">Inté.</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $block->price_integration }}{{ $unit }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase w-14">Champs</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $block->price_field_creation }}{{ $unit }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase w-14">Contenu</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">{{ $block->price_content_management }}{{ $unit }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="edit({{ $block->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 inline-flex items-center gap-1">
                                    <x-fas-edit class="w-3.5 h-3.5" />
                                    Modifier
                                </button>
                                <button wire:click="duplicate({{ $block->id }})" class="text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 inline-flex items-center gap-1">
                                    <x-fas-copy class="w-3.5 h-3.5" />
                                    Dupliquer
                                </button>
                                <button wire:click="delete({{ $block->id }})" onclick="confirm('Supprimer « {{ addslashes($block->name) }} » ?') || event.stopImmediatePropagation()" class="text-red-500 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 inline-flex items-center gap-1">
                                    <x-fas-trash class="w-3.5 h-3.5" />
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <x-fas-cubes class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                            <p class="text-gray-700 dark:text-gray-300 font-bold text-lg mb-1">Catalogue vide</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6">Ajoutez votre premier bloc pour commencer à construire vos estimations.</p>
                            <button wire:click="$set('showForm', true)" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                <x-fas-plus class="w-4 h-4" />
                                Créer mon premier bloc
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $blocks->links() }}
    </div>
</div>
