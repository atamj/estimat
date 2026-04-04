<div class="space-y-6">

    {{-- Explication --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 flex gap-4">
        <x-fas-lightbulb class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
        <div class="text-sm text-blue-800 dark:text-blue-300 space-y-1">
            <p class="font-bold">À quoi sert une Base Technique ?</p>
            <p>C'est le <strong>socle de départ</strong> de chaque projet : installation de l'environnement, configuration initiale, mise en place de la structure avant le développement. Elle est ajoutée automatiquement au début de chaque estimation.</p>
            <p>Définissez un <strong>nombre d'heures</strong> (estimations À l'heure) et/ou un <strong>prix forfaitaire par devise</strong> (estimations Forfait). Vous pouvez définir un prix différent pour chaque devise.</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes Bases Techniques</h2>
        @if(!$showForm)
            <button wire:click="$set('showForm', true)" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center shadow-sm transition-colors">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Nouvelle Base
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
                {{ $editingSetupId ? 'Modifier la base technique' : 'Créer une nouvelle base technique' }}
            </h2>
            <form wire:submit.prevent="save" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="type" placeholder="ex: Nouveau projet WordPress, Setup Laravel..."
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                        @error('type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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

                {{-- Estimation à l'heure --}}
                <div class="p-4 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Estimation à l'heure</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nombre d'heures ajouté aux estimations en mode À l'heure</p>
                        </div>
                        @if(!$showHoursField)
                            <button type="button" wire:click="$set('showHoursField', true)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-bold text-blue-600 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <x-fas-plus class="w-3.5 h-3.5" />
                                Ajouter des heures
                            </button>
                        @endif
                    </div>
                    @if($showHoursField)
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <input type="number" step="0.01" wire:model="fixed_hours" placeholder="0"
                                       class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 pr-8 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 font-bold pointer-events-none">h</span>
                            </div>
                            <button type="button" wire:click="$set('showHoursField', false); $set('fixed_hours', null)"
                                    class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                <x-fas-times class="w-4 h-4" />
                            </button>
                        </div>
                        @error('fixed_hours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @endif
                </div>

                {{-- Prix forfaitaires --}}
                <div class="p-4 bg-slate-50 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Prix forfaitaires</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Un prix par devise, ajouté aux estimations en mode Forfait</p>
                        </div>
                        @if(!$showPriceForm && count($this->availableCurrencies()) > 0)
                            <button type="button" wire:click="$set('showPriceForm', true)"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-bold text-blue-600 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-700 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <x-fas-plus class="w-3.5 h-3.5" />
                                Ajouter un prix
                            </button>
                        @endif
                    </div>

                    {{-- Liste des prix existants --}}
                    @if(count($prices) > 0)
                        <div class="space-y-2">
                            @foreach($prices as $index => $priceEntry)
                                @php $sym = \App\Enums\Currency::tryFrom($priceEntry['currency'])?->symbol() ?? $priceEntry['currency']; @endphp
                                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg px-3 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded shrink-0">
                                        {{ $priceEntry['currency'] }}
                                    </span>
                                    <div class="relative flex-1">
                                        <input type="number" step="0.01" wire:model="prices.{{ $index }}.price"
                                               class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg p-2 pr-8 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800">
                                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 text-xs font-bold pointer-events-none">{{ $sym }}</span>
                                    </div>
                                    <button type="button" wire:click="removePrice({{ $index }})"
                                            class="text-gray-400 hover:text-red-500 transition-colors p-1 shrink-0">
                                        <x-fas-times class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Formulaire d'ajout --}}
                    @if($showPriceForm)
                        <div class="flex items-end gap-3 p-3 bg-white dark:bg-gray-800 border-2 border-blue-200 dark:border-blue-700 rounded-lg">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Devise</label>
                                <select wire:model="newPriceCurrency"
                                        class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:border-blue-500">
                                    @foreach($this->availableCurrencies() as $currency)
                                        <option value="{{ $currency->value }}">{{ $currency->label() }}</option>
                                    @endforeach
                                </select>
                                @error('newPriceCurrency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1">Montant</label>
                                <div class="relative">
                                    <input type="number" step="0.01" wire:model="newPriceAmount" placeholder="0.00"
                                           class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2 pr-8 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:border-blue-500">
                                    <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 text-xs font-bold pointer-events-none">
                                        {{ \App\Enums\Currency::tryFrom($newPriceCurrency)?->symbol() ?? $newPriceCurrency }}
                                    </span>
                                </div>
                                @error('newPriceAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="addPrice"
                                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold transition-colors">
                                    Ajouter
                                </button>
                                <button type="button" wire:click="$set('showPriceForm', false)"
                                        class="px-3 py-2 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm transition-colors">
                                    Annuler
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(count($prices) === 0 && !$showPriceForm)
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
                        {{ $editingSetupId ? 'Mettre à jour' : 'Créer la base' }}
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
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">À l'heure</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prix forfaitaires</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($setups as $setup)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                            {{ $setup->type }}
                        </td>
                        <td class="px-6 py-4">
                            @if($setup->projectType)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 rounded">
                                    @if($setup->projectType->icon)
                                        <x-dynamic-component :component="$setup->projectType->icon" class="w-3 h-3" />
                                    @endif
                                    {{ $setup->projectType->name }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded">Générique</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            @if($setup->fixed_hours > 0)
                                {{ number_format($setup->fixed_hours, 1) }} h
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($setup->prices->count() > 0)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($setup->prices as $price)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded border border-emerald-200 dark:border-emerald-800">
                                            {{ number_format($price->price, 2) }}
                                            {{ \App\Enums\Currency::tryFrom($price->currency)?->symbol() ?? $price->currency }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center gap-3">
                                <button wire:click="edit({{ $setup->id }})" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center gap-1 text-sm font-medium transition-colors">
                                    <x-fas-edit class="w-4 h-4" />
                                    Modifier
                                </button>
                                <button wire:click="duplicate({{ $setup->id }})" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 inline-flex items-center gap-1 text-sm transition-colors">
                                    <x-fas-copy class="w-4 h-4" />
                                    Dupliquer
                                </button>
                                <button wire:click="delete({{ $setup->id }})" onclick="confirm('Supprimer cette base technique ?') || event.stopImmediatePropagation()"
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
                                <x-fas-cog class="w-8 h-8 text-blue-400" />
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 font-bold text-lg mb-1">Aucune base technique</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6 max-w-sm mx-auto">Créez votre première base pour qu'elle soit automatiquement ajoutée au début de vos estimations.</p>
                            <button wire:click="$set('showForm', true)" class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                                <x-fas-plus class="w-4 h-4" />
                                Créer ma première base
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
