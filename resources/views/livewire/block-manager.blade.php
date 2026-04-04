<div class="space-y-6">

    {{-- Explication --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-4">
        <x-fas-lightbulb class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
        <div class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
            <p class="font-bold">À quoi servent les Blocs ?</p>
            <p>Un bloc représente un <strong>composant de votre projet</strong> (ex: Page d'accueil, Menu, Formulaire de contact…). Chaque bloc contient 4 tarifs qui sont calculés différemment :</p>
            <ul class="list-disc list-inside space-y-0.5 mt-1">
                <li><strong>Programmation & Intégration</strong> — comptés <em>une seule fois</em> par type de bloc dans le devis</li>
                <li><strong>Création de champs</strong> — multiplié par la <em>quantité</em> du bloc</li>
                <li><strong>Gestion de contenu</strong> — multiplié par la quantité <em>et</em> le nombre de pages similaires</li>
            </ul>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mon Catalogue de Blocs</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouveau Bloc
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulaire --}}
    @if($showForm)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-t-4 border-blue-600">
            <h2 class="text-xl font-semibold mb-6 text-blue-800 dark:text-blue-400">
                {{ $editingBlockId ? 'Modifier le bloc' : 'Créer un nouveau bloc' }}
            </h2>
            <form wire:submit.prevent="save" class="space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom du bloc <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="ex: Page d'accueil, Menu de navigation, Formulaire de contact..."
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                            Description <span class="text-gray-400 dark:text-gray-500 font-normal">(optionnel — aide à identifier le bloc dans le catalogue)</span>
                        </label>
                        <textarea wire:model="description" rows="2" placeholder="ex: Page principale avec hero, présentation des services et CTA"
                                  class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Type de projet</label>
                        <select wire:model="project_type_id"
                                class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                            <option value="">Générique — visible pour tous les projets</option>
                            @foreach($projectTypes as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }} uniquement</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Estimation à l'heure --}}
                <div class="p-4 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Estimation à l'heure</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tarifs en heures pour les estimations À l'heure</p>
                        </div>
                        @if(!$this->hasHoursSet() && !$showHoursForm)
                            <button type="button" wire:click="$set('showHoursForm', true)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-bold text-blue-600 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <x-fas-plus class="w-3.5 h-3.5" />
                                Ajouter des heures
                            </button>
                        @endif
                    </div>

                    @php $hoursIndex = array_search('HOUR', array_column($priceSets, 'currency')); @endphp
                    @if($hoursIndex !== false)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 space-y-2">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarifs en heures</span>
                                <button type="button" wire:click="removePriceSet({{ $hoursIndex }})"
                                        class="text-gray-400 hover:text-red-500 transition-colors">
                                    <x-fas-times class="w-3.5 h-3.5" />
                                </button>
                            </div>
                            @include('livewire.partials.price-set-inputs', ['model' => 'priceSets.' . $hoursIndex, 'unit' => 'h'])
                        </div>
                    @endif

                    @if($showHoursForm)
                        <div class="p-3 bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-lg space-y-3">
                            <p class="text-xs font-bold text-gray-600 dark:text-gray-400">Saisir les tarifs en heures</p>
                            @include('livewire.partials.price-set-inputs', ['model' => 'newHoursValues', 'unit' => 'h'])
                            <div class="flex gap-2 justify-end pt-1">
                                <button type="button" wire:click="addHours"
                                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold transition-colors">
                                    Ajouter
                                </button>
                                <button type="button" wire:click="$set('showHoursForm', false)"
                                        class="px-3 py-1.5 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Prix forfaitaires --}}
                <div class="p-4 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Prix forfaitaires</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Un prix par devise pour les estimations Forfait</p>
                        </div>
                        @if(!$showPriceSetForm && count($this->availableCurrencies()) > 0)
                            <button type="button" wire:click="$set('showPriceSetForm', true)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-bold text-blue-600 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <x-fas-plus class="w-3.5 h-3.5" />
                                Ajouter un prix
                            </button>
                        @endif
                    </div>

                    @foreach($priceSets as $index => $set)
                        @if($set['currency'] !== 'HOUR')
                            @php $symbol = \App\Enums\Currency::tryFrom($set['currency'])?->symbol() ?? $set['currency']; @endphp
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-3 space-y-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 dark:text-blue-400">
                                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/40 rounded">{{ $set['currency'] }}</span>
                                        {{ $symbol }}
                                    </span>
                                    <button type="button" wire:click="removePriceSet({{ $index }})"
                                            class="text-gray-400 hover:text-red-500 transition-colors">
                                        <x-fas-times class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                                @include('livewire.partials.price-set-inputs', ['model' => 'priceSets.' . $index, 'unit' => $symbol])
                            </div>
                        @endif
                    @endforeach

                    @if($showPriceSetForm)
                        <div class="p-3 bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-lg space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Devise</label>
                                <select wire:model="newPriceSetCurrency"
                                        class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:border-blue-500">
                                    @foreach($this->availableCurrencies() as $currency)
                                        <option value="{{ $currency->value }}">{{ $currency->label() }}</option>
                                    @endforeach
                                </select>
                                @error('newPriceSetCurrency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            @php $newSymbol = \App\Enums\Currency::tryFrom($newPriceSetCurrency)?->symbol() ?? $newPriceSetCurrency; @endphp
                            @include('livewire.partials.price-set-inputs', ['model' => 'newPriceSetValues', 'unit' => $newSymbol])
                            <div class="flex gap-2 justify-end pt-1">
                                <button type="button" wire:click="addPriceSet"
                                        class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold transition-colors">
                                    Ajouter
                                </button>
                                <button type="button" wire:click="$set('showPriceSetForm', false)"
                                        class="px-3 py-1.5 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(collect($priceSets)->where('currency', '!=', 'HOUR')->isEmpty() && !$showPriceSetForm)
                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-2">Aucun prix forfaitaire défini</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="resetFields"
                            class="px-4 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold flex items-center gap-2 transition-colors shadow-sm">
                        <x-fas-save class="w-4 h-4" />
                        {{ $editingBlockId ? 'Mettre à jour' : 'Créer le bloc' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Liste --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700 flex flex-wrap justify-between items-center gap-3">
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $blocks->total() }} bloc(s) au total
            </p>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Filtrer :</label>
                <select wire:model.live="filter_project_type" class="text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg p-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="">Tous les blocs</option>
                    <option value="null">Génériques uniquement</option>
                    @foreach($projectTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bloc</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Technologie</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tarifs disponibles</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($blocks as $block)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $block->name }}</div>
                            @if($block->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 max-w-xs">{{ Str::limit($block->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($block->projectType)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded">
                                    @if($block->projectType->icon)
                                        <x-dynamic-component :component="$block->projectType->icon" class="w-3 h-3" />
                                    @endif
                                    {{ $block->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($block->priceSets->isEmpty())
                                <span class="text-xs text-amber-600 dark:text-amber-400 font-bold">Aucun tarif</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($block->priceSets as $priceSet)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $priceSet->currency === 'HOUR' ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' }}">
                                            {{ $priceSet->currency === 'HOUR' ? 'Heures' : $priceSet->currency }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="edit({{ $block->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 inline-flex items-center gap-1 text-sm font-medium transition-colors">
                                    <x-fas-edit class="w-3.5 h-3.5" />
                                    Modifier
                                </button>
                                <button wire:click="duplicate({{ $block->id }})" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 inline-flex items-center gap-1 text-sm transition-colors">
                                    <x-fas-copy class="w-3.5 h-3.5" />
                                    Dupliquer
                                </button>
                                <button wire:click="delete({{ $block->id }})" onclick="confirm('Supprimer « {{ addslashes($block->name) }} » ?') || event.stopImmediatePropagation()"
                                        class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 inline-flex items-center gap-1 text-sm transition-colors">
                                    <x-fas-trash class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-fas-cubes class="w-8 h-8 text-blue-400" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 font-bold text-lg mb-1">Catalogue vide</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6 max-w-sm mx-auto">Ajoutez votre premier bloc pour commencer à construire vos estimations.</p>
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
