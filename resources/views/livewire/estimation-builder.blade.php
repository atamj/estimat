<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ showSaveAsTemplateModal: false, templateName: '' }">
    <div class="lg:col-span-2 space-y-8">
        <!-- Bandeau d'infos Technologie et Type (Statique) -->
        <div class="bg-blue-900 text-white p-4 rounded-lg shadow-md flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-6">
                <!-- Technologie -->
                <div class="flex items-center">
                    @if($project_type_id)
                        @php $pt = \App\Models\ProjectType::find($project_type_id); @endphp
                        @if($pt && $pt->icon)
                            <div class="bg-white p-2 rounded-lg shadow-sm mr-3">
                                <x-dynamic-component :component="$pt->icon" class="w-6 h-6 text-blue-900" />
                            </div>
                        @endif
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Technologie</p>
                            <p class="font-bold text-lg">{{ $pt ? $pt->name : 'Générique' }}</p>
                        </div>
                    @else
                        <div class="bg-blue-800 p-2 rounded-lg shadow-sm mr-3">
                            <x-fas-question class="w-6 h-6 text-blue-300" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Technologie</p>
                            <p class="font-bold text-lg italic">Générique</p>
                        </div>
                    @endif
                </div>

                <!-- Séparateur -->
                <div class="hidden md:block h-10 w-px bg-blue-700"></div>

                <!-- Type d'estimation -->
                <div class="flex items-center">
                    <div class="bg-blue-800 p-2 rounded-lg shadow-sm mr-3">
                        @if($type == 'hour')
                            <x-fas-clock class="w-6 h-6 text-blue-300" />
                        @else
                            <x-fas-euro-sign class="w-6 h-6 text-blue-300" />
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Mode de facturation</p>
                        <p class="font-bold text-lg">
                            {{ $type == 'hour' ? 'À l\'heure' : 'Forfaitaire' }}
                        </p>
                    </div>
                </div>

                <!-- Séparateur -->
                <div class="hidden md:block h-10 w-px bg-blue-700"></div>

                <!-- Résumé Dashboard -->
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Pages</p>
                        <p class="font-bold text-lg">{{ $estimation->regularPages->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Blocs</p>
                        <p class="font-bold text-lg">{{ $estimation->pages->flatMap->blocks->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="flex items-center gap-2">
                <button @click="showSaveAsTemplateModal = true; templateName = ''" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-bold text-sm flex items-center transition-colors">
                    <x-fas-layer-group class="w-4 h-4 mr-2" />
                    Sauver comme gabarit
                </button>
                @if((isset($totals['total_price']) && $totals['total_price'] > 0) || (isset($totals['total_time']) && $totals['total_time'] > 0 && $type === 'hour'))
                    <a href="{{ route('estimations.pdf', $estimation) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md font-bold text-sm flex items-center transition-colors">
                        <x-fas-file-pdf class="w-4 h-4 mr-2" />
                        Aperçu PDF
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md" x-data="{ showProject: @js(!empty($project_name)), showHourlyRate: @js(!empty($hourly_rate)) }">
            <h2 class="text-xl font-semibold mb-4 text-blue-800 dark:text-blue-400 border-b dark:border-gray-700 pb-2 flex items-center">
                <x-fas-cog class="w-5 h-5 mr-2" />
                1. Configuration du Projet
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div x-data="{ isEditingClient: false }">
                    <label class="block text-sm font-bold text-gray-700">Nom du client</label>
                    <div class="mt-1 flex items-center justify-between group h-10">
                        <div x-show="!isEditingClient" class="flex items-center space-x-2">
                            <span class="text-lg font-medium text-gray-900">{{ $client_name }}</span>
                            <button @click="isEditingClient = true" class="text-gray-400 hover:text-blue-600 transition-colors p-1" title="Modifier le nom du client">
                                <x-fas-edit class="w-4 h-4" />
                            </button>
                        </div>
                        <div x-show="isEditingClient" @click.away="isEditingClient = false" class="w-full" x-cloak>
                            <input type="text"
                                   wire:model.blur="client_name"
                                   @keydown.enter="isEditingClient = false"
                                   class="block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2"
                                   x-ref="clientInput"
                                   x-init="$watch('isEditingClient', value => { if(value) { setTimeout(() => $refs.clientInput.focus(), 50) } })">
                        </div>
                    </div>
                </div>

                <div x-show="showProject" x-transition>
                    <label class="block text-sm font-bold text-gray-700">Projet</label>
                    <input type="text" wire:model.blur="project_name" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2">
                </div>

                <div x-show="showHourlyRate" x-transition>
                    <label class="block text-sm font-bold text-gray-700">Taux horaire (optionnel)</label>
                    <input type="number" wire:model.blur="hourly_rate" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2">
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Base technique & Mise en place</label>
                        <select wire:change="handleSetupSelection($event.target.value)" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 p-2">
                            <option value="">Aucune base</option>
                            @foreach($setups as $setup)
                                <option value="{{ $setup->id }}" {{ $setup_id == $setup->id ? 'selected' : '' }}>{{ $setup->type }}</option>
                            @endforeach
                            <option value="new_setup" class="font-bold text-green-600">✨ Ajouter une nouvelle base technique...</option>
                        </select>
                    </div>

                    @if($setup_id)
                        @php $currentSetup = \App\Models\Setup::find($setup_id); @endphp
                        @if($currentSetup)
                            <div class="p-4 bg-blue-50 rounded-xl border-2 border-blue-200 shadow-inner space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-blue-800 text-xs font-bold uppercase tracking-wider">
                                        <x-fas-info-circle class="w-4 h-4 mr-2" />
                                        Détails de la base technique
                                    </div>
                                    <button type="button" wire:click="toggleSetupEditing" class="text-blue-600 hover:text-blue-800 text-[10px] font-bold uppercase flex items-center">
                                        <x-fas-edit class="w-3 h-3 mr-1" />
                                        {{ $isSetupEditing ? 'Terminer l\'édition' : 'Modifier' }}
                                    </button>
                                </div>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    Le système sélectionne automatiquement entre le <strong>prix forfaitaire</strong> ou le <strong>nombre d'heures</strong> en fonction du type d'estimation choisi pour le projet (Forfait ou À l'heure).
                                </p>
                                <div class="grid grid-cols-1 gap-4">
                                    @if($type == 'fixed')
                                    @php $setupPrice = $currentSetup->load('prices')->priceForCurrency($estimation->currency ?? 'EUR'); @endphp
                                    <div class="bg-white p-3 rounded-lg border-2 border-blue-500 shadow-sm">
                                        <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700">Prix Forfaitaire</label>
                                        @if($setupPrice > 0)
                                            <div class="text-lg font-black text-blue-900">{{ number_format($setupPrice, 2) }} {{ $currencySymbol }}</div>
                                            <span class="text-[9px] text-blue-500 font-bold uppercase">Tarif standard</span>
                                        @else
                                            <span class="text-[9px] text-orange-500 font-bold uppercase">Non défini pour cette devise — configurez dans les bases techniques</span>
                                        @endif
                                    </div>
                                    @endif

                                    @if($type == 'hour')
                                    <div class="bg-white p-3 rounded-lg border-2 border-blue-500 shadow-sm">
                                        <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700">Nombre d'heures</label>
                                        @if($currentSetup->fixed_hours > 0 && !$isSetupEditing)
                                            <div class="text-lg font-black text-blue-900">{{ number_format($currentSetup->fixed_hours, 2) }} h</div>
                                            <span class="text-[9px] text-blue-500 font-bold uppercase">Temps standard (verrouillé)</span>
                                        @else
                                            <div class="relative mt-1">
                                                <input type="number" step="0.01"
                                                       value="{{ $currentSetup->fixed_hours }}"
                                                       onchange="@this.updateSetupValue({{ $currentSetup->id }}, 'fixed_hours', this.value)"
                                                       class="block w-full border-2 border-blue-300 rounded-md shadow-sm text-sm p-2 pr-7 focus:border-blue-500 bg-white">
                                                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-blue-400 font-bold">h</div>
                                            </div>
                                            @if($currentSetup->fixed_hours > 0)
                                                <span class="text-[9px] text-blue-500 font-bold uppercase">Modification temporaire autorisée</span>
                                            @else
                                                <span class="text-[9px] text-orange-500 font-bold uppercase">Non défini - Saisissez une valeur</span>
                                            @endif
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="md:col-span-2 flex items-center space-x-4">
                    <template x-if="!showProject">
                        <button type="button" @click="showProject = true" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center transition-colors">
                            <x-fas-plus class="w-3 h-3 mr-2" />
                            Ajouter un projet
                        </button>
                    </template>

                    <template x-if="!showHourlyRate">
                        <button type="button" @click="showHourlyRate = true" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center transition-colors">
                            <x-fas-plus class="w-3 h-3 mr-2" />
                            Ajouter un taux horaire
                        </button>
                    </template>
                </div>

                @if(!$hourly_rate)
                    <div class="md:col-span-2 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-fas-exclamation-triangle class="h-5 w-5 text-yellow-400" />
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    @if($type == 'fixed')
                                        Puisque c'est une <strong>estimation forfaitaire</strong> sans taux horaire, seuls les blocs et add-ons <strong>au forfait</strong> sont disponibles. Renseignez un taux pour accéder également aux blocs et add-ons à l'heure.
                                    @else
                                        Puisque c'est une <strong>estimation à l'heure</strong> sans taux horaire, seuls les blocs et add-ons <strong>à l'heure</strong> sont disponibles. Renseignez un taux pour accéder également aux blocs et add-ons au forfait.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Zone B: Header, Pages & Footer & Blocs -->
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-blue-800 dark:text-blue-400 flex items-center">
                    <x-fas-layer-group class="w-5 h-5 mr-2" />
                    2. Structure du site
                </h2>
                <div class="flex space-x-2">
                    <button wire:click="addPage" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm flex items-center">
                        <x-fas-plus class="w-3 h-3 mr-2" />
                        Ajouter une page
                    </button>
                </div>
            </div>

            <!-- Site Header -->
            @if($estimation->headerPage)
                @include('livewire.partials.builder-page', ['page' => $estimation->headerPage, 'isGlobal' => true])
            @endif

            <!-- Regular Pages -->
            @foreach($estimation->regularPages as $page)
                @include('livewire.partials.builder-page', ['page' => $page, 'isGlobal' => false])
            @endforeach

            <!-- Site Footer -->
            @if($estimation->footerPage)
                @include('livewire.partials.builder-page', ['page' => $estimation->footerPage, 'isGlobal' => true])
            @endif
        </div>

        <!-- Zone C: Options globales -->
        @php
            $user = auth()->user();
            $subscription = $user?->activeSubscription;
            $plan = $subscription?->plan;
            $canTranslate = $plan ? $plan->has_translation_module : true;
        @endphp
        <div class="bg-white p-6 rounded-lg shadow-md {{ !$canTranslate ? 'opacity-75 relative' : '' }}">
            <h2 class="text-xl font-semibold mb-4 text-blue-800 dark:text-blue-400 border-b dark:border-gray-700 pb-2 flex items-center justify-between">
                <div class="flex items-center">
                    <x-fas-plus-circle class="w-5 h-5 mr-2" />
                    3. Options & Traduction
                </div>
                @if(!$canTranslate)
                    <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-1 rounded-full uppercase font-bold flex items-center gap-1">
                        <x-fas-lock class="w-2 h-2" /> Plan Pro requis
                    </span>
                @endif
            </h2>

            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" wire:model.live="translation_enabled" id="translation_enabled"
                           class="rounded text-blue-600" {{ !$canTranslate ? 'disabled' : '' }}>
                    <label for="translation_enabled" class="font-medium text-gray-700 {{ !$canTranslate ? 'text-gray-400' : '' }}">Ce site sera traduit</label>
                </div>

                @if($translation_enabled)
                    <div class="ml-6 mt-4 p-6 bg-blue-50 rounded-xl border-2 border-blue-200 shadow-inner">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <!-- Sélecteur de langues -->
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-blue-800 mb-3 flex items-center">
                                    <x-fas-language class="w-4 h-4 mr-2" />
                                    Nombre de langues du projet
                                </label>
                                <div class="flex items-center space-x-3">
                                    <button type="button"
                                            wire:click="$set('translation_languages_count', {{ max(1, $translation_languages_count - 1) }})"
                                            class="w-10 h-10 rounded-full bg-white border-2 border-blue-300 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors shadow-sm">
                                        <x-fas-minus class="w-3 h-3" />
                                    </button>

                                    <div class="w-16 h-12 bg-white border-2 border-blue-400 rounded-lg flex items-center justify-center text-xl font-black text-blue-900 shadow-sm">
                                        {{ $translation_languages_count }}
                                    </div>

                                    <button type="button"
                                            wire:click="$set('translation_languages_count', {{ $translation_languages_count + 1 }})"
                                            class="w-10 h-10 rounded-full bg-white border-2 border-blue-300 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors shadow-sm">
                                        <x-fas-plus class="w-3 h-3" />
                                    </button>

                                    @if($translation_languages_count > 1)
                                        <span class="text-xs font-bold text-orange-600 bg-orange-100 px-2 py-1 rounded-full animate-pulse">
                                            Surcharge x2 activée
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Valeurs éditables -->
                            <div class="flex flex-wrap gap-4">
                                @if($type == 'fixed' && (!$isPriceLocked || $translation_fixed_price == 0))
                                <div class="w-40">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700">Coût Forfaitaire</label>
                                    <div class="relative mt-1">
                                        <input type="number" step="0.01" wire:model.blur="translation_fixed_price"
                                               class="block w-full border-2 border-blue-300 rounded-md shadow-sm text-sm p-2 pl-7 focus:border-blue-500 bg-white">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-blue-400 font-bold">{{ $currencySymbol }}</div>
                                    </div>
                                </div>
                                @endif

                                @if($type == 'hour' && (!$isHoursLocked || $translation_fixed_hours == 0))
                                <div class="w-40">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700">Heures Fixes</label>
                                    <div class="relative mt-1">
                                        <input type="number" step="0.01" wire:model.blur="translation_fixed_hours"
                                               class="block w-full border-2 border-blue-300 rounded-md shadow-sm text-sm p-2 pr-7 focus:border-blue-500 bg-white">
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-blue-400 font-bold">h</div>
                                    </div>
                                </div>
                                @endif

                                @if(!$isPercentageLocked || $translation_percentage == 0)
                                <div class="w-40">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700">Surcharge %</label>
                                    <div class="relative mt-1">
                                        <input type="number" step="0.01" wire:model.blur="translation_percentage"
                                               class="block w-full border-2 border-blue-300 rounded-md shadow-sm text-sm p-2 pr-7 focus:border-blue-500 bg-white">
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-blue-400 font-bold">%</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6">
                    <label class="block font-medium text-gray-700 mb-2">Add-ons disponibles</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($addons as $addon)
                            <div wire:click="toggleAddon({{ $addon->id }})"
                                 class="cursor-pointer p-3 rounded-md border {{ $estimation->addons->contains($addon->id) ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 border-gray-200 text-gray-700' }}">
                                <div class="text-sm font-bold">{{ $addon->name }}</div>
                                <div class="text-xs {{ $estimation->addons->contains($addon->id) ? 'text-blue-100' : 'text-gray-500' }}">
                                    @if($addon->type == 'fixed_price')
                                        {{ $addon->value }} {{ $currencySymbol }}
                                    @elseif($addon->type == 'fixed_hours')
                                        {{ $addon->value }} h
                                    @else
                                        {{ $addon->value }} % ({{ $addon->calculation_base }})
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar: Totaux -->
    <div class="lg:col-span-1">
        <div class="bg-blue-900 text-white p-6 rounded-lg shadow-lg sticky top-8">
            <h2 class="text-xl font-bold mb-6 border-b border-blue-800 pb-2 text-center flex items-center justify-center dark:text-gray-100">
                <x-fas-calculator class="w-5 h-5 mr-2" />
                Récapitulatif
            </h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-blue-800 pb-1">
                    <span>Base Technique :</span>
                    <span class="font-medium">{{ number_format($totals['setup'], 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-blue-800 pb-1">
                    <span>Programmation :</span>
                    <span class="font-medium">{{ number_format($totals['programming'], 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-blue-800 pb-1">
                    <span>Intégration :</span>
                    <span class="font-medium">{{ number_format($totals['integration'], 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-blue-800 pb-1">
                    <span>Création Champs :</span>
                    <span class="font-medium">{{ number_format($totals['field_creation'], 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-blue-800 pb-1">
                    <span>Gestion Contenu :</span>
                    <span class="font-medium">{{ number_format($totals['content_management'], 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-blue-800 pb-1 text-blue-300">
                    <span>Traduction :</span>
                    <span class="font-medium">{{ number_format($totals['translation'], 2) }}</span>
                </div>
                <div class="flex justify-between border-b border-blue-800 pb-1 text-blue-300">
                    <span>Add-ons :</span>
                    <span class="font-medium">{{ number_format($totals['addons'], 2) }}</span>
                </div>

                <div class="mt-8 pt-4 border-t-2 border-blue-700">
                    @if($estimation->type == 'hour')
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total Temps :</span>
                            <span>{{ number_format($totals['total_time'], 2) }} h</span>
                        </div>
                        @if($estimation->hourly_rate)
                            <div class="flex justify-between text-2xl font-black text-green-400 mt-2">
                                <span>Total Prix :</span>
                                <span>{{ number_format($totals['total_price'], 2) }} {{ $currencySymbol }}</span>
                            </div>
                        @endif
                    @else
                        <div class="flex justify-between text-2xl font-black text-green-400">
                            <span>Total Prix :</span>
                            <span>{{ number_format($totals['total_price'], 2) }} {{ $currencySymbol }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if((isset($totals['total_price']) && $totals['total_price'] > 0) || (isset($totals['total_time']) && $totals['total_time'] > 0 && $type === 'hour'))
                <a href="{{ route('estimations.pdf', $estimation) }}" target="_blank" class="w-full block text-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-md mt-8 transition duration-200 flex items-center justify-center">
                    <x-fas-file-pdf class="w-5 h-5 mr-2" />
                    Générer le PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Modal Sauvegarder comme gabarit -->
    <div x-show="showSaveAsTemplateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-md mx-4" @click.stop>
            <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-gray-100 flex items-center">
                <x-fas-layer-group class="w-5 h-5 mr-2 text-purple-600" />
                Sauvegarder comme gabarit
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">La structure (pages, blocs, add-ons) sera copiée dans un nouveau gabarit réutilisable.</p>
            <form method="POST" action="{{ route('estimations.save-as-template', $estimation) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom du gabarit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="templateName" required
                           placeholder="ex: Landing Page, E-commerce..."
                           class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-purple-500">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showSaveAsTemplateModal = false"
                            class="px-4 py-2 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 font-bold transition-colors">
                        Créer le gabarit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Nouveau Bloc -->
    @if($showBlockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-xl w-full max-w-lg mx-4">
                <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-gray-100">Créer un nouveau Bloc</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Les tarifs seront configurés dans <strong>Paramètres → Blocs</strong>.</p>
                <form wire:submit.prevent="createBlock" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="newBlock.name" placeholder="ex: Page d'accueil, Menu de navigation..."
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500">
                        @error('newBlock.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Technologie</label>
                        <select wire:model="newBlock.project_type_id"
                                class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500">
                            <option value="">Générique (toutes technologies)</option>
                            @foreach($projectTypes as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('showBlockModal', false)"
                                class="px-4 py-2 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold transition-colors">
                            Créer le Bloc
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Nouvelle Base Technique -->
    <div x-data="{ open: @js($showSetupModal) }" x-show="open" x-on:setup-created.window="open = false" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="createSetup">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-blue-800 dark:text-blue-400 border-b dark:border-gray-700 pb-2 mb-4">✨ Ajouter une nouvelle base technique</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Nom / Type de base</label>
                                <input type="text" wire:model="newSetup.type" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm p-2" placeholder="ex: Refonte complète, Maintenance...">
                                @error('newSetup.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Prix Forfaitaire ({{ $currencySymbol }})</label>
                                    <input type="number" step="0.01" wire:model="newSetup.fixed_price" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700">Nombre d'heures (h)</label>
                                    <input type="number" step="0.01" wire:model="newSetup.fixed_hours" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm p-2">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">Technologie</label>
                                <select wire:model="newSetup.project_type_id" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm p-2">
                                    <option value="">Générique</option>
                                    @foreach($projectTypes as $pt)
                                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Enregistrer
                        </button>
                        <button type="button" wire:click="$set('showSetupModal', false)" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
