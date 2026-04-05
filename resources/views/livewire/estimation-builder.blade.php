<div class="grid grid-cols-1 lg:grid-cols-3 gap-8"
     x-data="{
         step: 1,
         toggle(n) { this.step = this.step === n ? null : n },
         next(n) { this.step = n + 1; this.$nextTick(() => { document.getElementById('step-' + (n+1))?.scrollIntoView({ behavior: 'smooth', block: 'start' }) }) }
     }">

    <div class="lg:col-span-2 space-y-4">

        {{-- Bandeau info --}}
        <div class="bg-blue-900 text-white p-4 rounded-lg shadow-md flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-6">
                @if($project_type_id)
                    @php $pt = \App\Models\ProjectType::find($project_type_id); @endphp
                    @if($pt?->icon)
                        <div class="flex items-center">
                            <div class="bg-white p-2 rounded-lg shadow-sm mr-3">
                                <x-dynamic-component :component="$pt->icon" class="w-6 h-6 text-blue-900" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Technologie</p>
                                <p class="font-bold">{{ $pt->name }}</p>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="hidden md:block h-8 w-px bg-blue-700"></div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Mode</p>
                    <p class="font-bold">{{ $type == 'hour' ? 'À l\'heure' : 'Forfaitaire' }}</p>
                </div>
                <div class="hidden md:block h-8 w-px bg-blue-700"></div>
                <div class="flex items-center gap-4 text-center">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Pages</p>
                        <p class="font-bold">{{ $estimation->regularPages->count() }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-blue-300 font-bold">Blocs</p>
                        <p class="font-bold">{{ $estimation->pages->flatMap->blocks->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-template-modal')"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-bold text-sm flex items-center transition-colors">
                    <x-fas-layer-group class="w-4 h-4 mr-2" />
                    Sauver comme gabarit
                </button>
                @if((isset($totals['total_price']) && $totals['total_price'] > 0) || (isset($totals['total_time']) && $totals['total_time'] > 0 && $type === 'hour'))
                    <a href="{{ route('estimations.pdf', $estimation) }}" target="_blank"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md font-bold text-sm flex items-center transition-colors">
                        <x-fas-file-pdf class="w-4 h-4 mr-2" />
                        PDF
                    </a>
                @endif
            </div>
        </div>

        {{-- Barre de progression --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                @foreach([
                    [1, 'Configuration', 'fas-cog'],
                    [2, 'En-tête', 'fas-arrow-up'],
                    [3, 'Pages', 'fas-layer-group'],
                    [4, 'Pied de page', 'fas-arrow-down'],
                    [5, 'Options', 'fas-plus-circle'],
                ] as [$n, $label, $icon])
                    <button @click="toggle({{ $n }})"
                            class="flex flex-col items-center gap-1 group cursor-pointer transition-colors"
                            :class="step >= {{ $n }} ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-600'">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-black transition-all border-2"
                             :class="step === {{ $n }} ? 'bg-blue-600 text-white border-blue-600 shadow-md' : (step > {{ $n }} ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 border-blue-300 dark:border-blue-700' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 border-gray-200 dark:border-gray-600')">
                            <template x-if="step > {{ $n }}">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </template>
                            <template x-if="step <= {{ $n }}">
                                <span>{{ $n }}</span>
                            </template>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider hidden sm:block">{{ $label }}</span>
                    </button>
                    @if($n < 5)
                        <div class="flex-1 h-0.5 mx-2 rounded-full transition-colors"
                             :class="step > {{ $n }} ? 'bg-blue-400' : 'bg-gray-200 dark:bg-gray-700'"></div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             ÉTAPE 1 : Configuration du projet
        ═══════════════════════════════════════════════════ --}}
        <div id="step-1" class="rounded-xl border-2 transition-all duration-200"
             :class="step === 1 ? 'border-blue-500 shadow-md' : 'border-gray-200 dark:border-gray-700'">

            {{-- En-tête accordéon --}}
            <button @click="toggle(1)" class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black shrink-0"
                         :class="step > 1 ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : 'bg-blue-600 text-white'">
                        <template x-if="step > 1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </template>
                        <template x-if="step <= 1"><span>1</span></template>
                    </div>
                    <span class="font-bold text-gray-800 dark:text-gray-100">Configuration du projet</span>
                    {{-- Résumé quand fermé --}}
                    <div x-show="step !== 1" class="flex items-center gap-2 flex-wrap">
                        @if($client_name)
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-full font-medium">{{ $client_name }}</span>
                        @endif
                        <span class="text-xs bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-full font-medium">{{ $type == 'hour' ? 'À l\'heure' : 'Forfait' }}</span>
                        @if($setup_id)
                            @php $s = \App\Models\Setup::find($setup_id); @endphp
                            @if($s)
                                <span class="text-xs bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">{{ $s->type }}</span>
                            @endif
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0"
                     :class="step === 1 ? 'rotate-180' : ''"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>

            {{-- Contenu étape 1 --}}
            <div x-show="step === 1"
                 class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-6 pb-6 pt-4"
                 x-data="{ showProject: @js(!empty($project_name)), showHourlyRate: @js(!empty($hourly_rate)) }">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div x-data="{ isEditingClient: false }">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom du client</label>
                        <div class="flex items-center space-x-2 group h-10">
                            <div x-show="!isEditingClient" class="flex items-center space-x-2">
                                <span class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $client_name }}</span>
                                <button @click="isEditingClient = true" class="text-gray-400 hover:text-blue-600 transition-colors p-1">
                                    <x-fas-edit class="w-4 h-4" />
                                </button>
                            </div>
                            <div x-show="isEditingClient" @click.away="isEditingClient = false" class="w-full" x-cloak>
                                <input type="text" wire:model.blur="client_name"
                                       @keydown.enter="isEditingClient = false"
                                       class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                       x-ref="clientInput"
                                       x-init="$watch('isEditingClient', v => { if(v) setTimeout(() => $refs.clientInput.focus(), 50) })">
                            </div>
                        </div>
                    </div>

                    <div x-show="showProject" x-transition>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom du projet</label>
                        <input type="text" wire:model.blur="project_name"
                               class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div x-show="showHourlyRate" x-transition>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Taux horaire (optionnel)</label>
                        <div class="relative">
                            <input type="number" wire:model.blur="hourly_rate"
                                   class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 pr-10 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 font-bold text-sm pointer-events-none">{{ $currencySymbol }}/h</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Base technique & Mise en place</label>
                        <select wire:change="handleSetupSelection($event.target.value)"
                                class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="">Aucune base</option>
                            @foreach($setups as $setup)
                                <option value="{{ $setup->id }}" {{ $setup_id == $setup->id ? 'selected' : '' }}>{{ $setup->type }}</option>
                            @endforeach
                            <option value="new_setup" class="font-bold text-green-600">✨ Ajouter une nouvelle base technique...</option>
                        </select>

                        @if($setup_id)
                            @php $currentSetup = \App\Models\Setup::find($setup_id); @endphp
                            @if($currentSetup)
                                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-800 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-300 flex items-center gap-1">
                                            <x-fas-info-circle class="w-3 h-3" /> Détails
                                        </span>
                                        <button type="button" wire:click="toggleSetupEditing" class="text-blue-600 hover:text-blue-800 text-[10px] font-bold uppercase flex items-center gap-1">
                                            <x-fas-edit class="w-3 h-3" />
                                            {{ $isSetupEditing ? 'Terminer' : 'Modifier' }}
                                        </button>
                                    </div>
                                    @if($type == 'fixed')
                                        @php $setupPrice = $currentSetup->load('prices')->priceForCurrency($estimation->currency ?? 'EUR'); @endphp
                                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border-2 border-blue-400">
                                            <label class="text-[10px] uppercase tracking-wider font-black text-blue-700 dark:text-blue-300">Prix Forfaitaire</label>
                                            @if($setupPrice > 0)
                                                <div class="text-lg font-black text-blue-900 dark:text-blue-100">{{ number_format($setupPrice, 2) }} {{ $currencySymbol }}</div>
                                            @else
                                                <span class="text-[9px] text-orange-500 font-bold uppercase">Non défini pour cette devise</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($type == 'hour')
                                        <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border-2 border-blue-400">
                                            <label class="text-[10px] uppercase tracking-wider font-black text-blue-700 dark:text-blue-300">Nombre d'heures</label>
                                            @if($currentSetup->fixed_hours > 0 && !$isSetupEditing)
                                                <div class="text-lg font-black text-blue-900 dark:text-blue-100">{{ number_format($currentSetup->fixed_hours, 2) }} h</div>
                                            @else
                                                <div class="relative mt-1">
                                                    <input type="number" step="0.01" value="{{ $currentSetup->fixed_hours }}"
                                                           onchange="@this.updateSetupValue({{ $currentSetup->id }}, 'fixed_hours', this.value)"
                                                           class="block w-full border-2 border-blue-300 rounded-md text-sm p-2 pr-7 bg-white dark:bg-gray-800 dark:text-gray-100">
                                                    <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-blue-400 font-bold pointer-events-none">h</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="md:col-span-2 flex items-center gap-4">
                        <template x-if="!showProject">
                            <button type="button" @click="showProject = true" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-1 transition-colors">
                                <x-fas-plus class="w-3 h-3" /> Ajouter un nom de projet
                            </button>
                        </template>
                        <template x-if="!showHourlyRate">
                            <button type="button" @click="showHourlyRate = true" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center gap-1 transition-colors">
                                <x-fas-plus class="w-3 h-3" /> Ajouter un taux horaire
                            </button>
                        </template>
                    </div>

                    @if(!$hourly_rate)
                        <div class="md:col-span-2 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                @if($type == 'fixed')
                                    Estimation <strong>forfaitaire</strong> sans taux horaire → seuls les blocs et add-ons au forfait sont disponibles.
                                @else
                                    Estimation <strong>à l'heure</strong> sans taux horaire → seuls les blocs et add-ons à l'heure sont disponibles.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="next(1)" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm flex items-center gap-2 transition-colors shadow-sm">
                        Continuer
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             ÉTAPE 2 : En-tête du site
        ═══════════════════════════════════════════════════ --}}
        @php $headerPage = $estimation->headerPage; $headerBlockCount = $headerPage?->blocks->count() ?? 0; @endphp
        <div id="step-2" class="rounded-xl border-2 transition-all duration-200"
             :class="step === 2 ? 'border-blue-500 shadow-md' : 'border-gray-200 dark:border-gray-700'">

            <button @click="toggle(2)" class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black shrink-0"
                         :class="step > 2 ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : (step === 2 ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')">
                        <template x-if="step > 2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></template>
                        <template x-if="step <= 2"><span>2</span></template>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-fas-arrow-up class="w-4 h-4 text-orange-500" />
                        <span class="font-bold text-gray-800 dark:text-gray-100">En-tête du site</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">(Header global)</span>
                    </div>
                    <div x-show="step !== 2">
                        @if($headerBlockCount > 0)
                            <span class="text-xs bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-medium">{{ $headerBlockCount }} bloc{{ $headerBlockCount > 1 ? 's' : '' }}</span>
                        @else
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-400 px-2 py-0.5 rounded-full">Vide</span>
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0"
                     :class="step === 2 ? 'rotate-180' : ''"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>

            <div x-show="step === 2" class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-6 pb-6 pt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Le header est partagé sur toutes les pages du site. Son coût de programmation n'est compté qu'une seule fois.</p>
                @if($headerPage)
                    @include('livewire.partials.builder-page', ['page' => $headerPage, 'isGlobal' => true])
                @endif
                <div class="mt-4 flex justify-end">
                    <button @click="next(2)" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm flex items-center gap-2 transition-colors shadow-sm">
                        Continuer <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             ÉTAPE 3 : Pages du site
        ═══════════════════════════════════════════════════ --}}
        @php
            $regularPages = $estimation->regularPages;
            $regularBlockCount = $regularPages->flatMap->blocks->count();
        @endphp
        <div id="step-3" class="rounded-xl border-2 transition-all duration-200"
             :class="step === 3 ? 'border-blue-500 shadow-md' : 'border-gray-200 dark:border-gray-700'">

            <div class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer" @click="toggle(3)">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black shrink-0"
                         :class="step > 3 ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : (step === 3 ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')">
                        <template x-if="step > 3"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></template>
                        <template x-if="step <= 3"><span>3</span></template>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-fas-layer-group class="w-4 h-4 text-blue-500" />
                        <span class="font-bold text-gray-800 dark:text-gray-100">Pages du site</span>
                    </div>
                    <div x-show="step !== 3" class="flex items-center gap-2">
                        <span class="text-xs bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-full font-medium">{{ $regularPages->count() }} page{{ $regularPages->count() > 1 ? 's' : '' }}</span>
                        @if($regularBlockCount > 0)
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">{{ $regularBlockCount }} blocs</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button x-show="step === 3" wire:click="addPage" @click.stop type="button"
                            class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 text-xs font-bold flex items-center gap-1 transition-colors">
                        <x-fas-plus class="w-3 h-3" /> Ajouter une page
                    </button>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0"
                         :class="step === 3 ? 'rotate-180' : ''"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>

            <div x-show="step === 3" class="bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 px-6 pb-6 pt-4 space-y-4">
                @if($regularPages->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-gray-400 dark:text-gray-500">
                        <x-fas-layer-group class="w-10 h-10 mb-3 opacity-30" />
                        <p class="text-sm font-medium">Aucune page pour le moment</p>
                        <button wire:click="addPage" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-bold flex items-center gap-2 transition-colors">
                            <x-fas-plus class="w-3 h-3" /> Ajouter ma première page
                        </button>
                    </div>
                @else
                    @foreach($regularPages as $page)
                        @include('livewire.partials.builder-page', ['page' => $page, 'isGlobal' => false])
                    @endforeach
                    <button wire:click="addPage" class="w-full border-2 border-dashed border-blue-300 dark:border-blue-700 text-blue-600 dark:text-blue-400 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition-colors">
                        <x-fas-plus class="w-3 h-3" /> Ajouter une page
                    </button>
                @endif
                <div class="flex justify-end pt-2">
                    <button @click="next(3)" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm flex items-center gap-2 transition-colors shadow-sm">
                        Continuer <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             ÉTAPE 4 : Pied de page
        ═══════════════════════════════════════════════════ --}}
        @php $footerPage = $estimation->footerPage; $footerBlockCount = $footerPage?->blocks->count() ?? 0; @endphp
        <div id="step-4" class="rounded-xl border-2 transition-all duration-200"
             :class="step === 4 ? 'border-blue-500 shadow-md' : 'border-gray-200 dark:border-gray-700'">

            <button @click="toggle(4)" class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black shrink-0"
                         :class="step > 4 ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400' : (step === 4 ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400')">
                        <template x-if="step > 4"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></template>
                        <template x-if="step <= 4"><span>4</span></template>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-fas-arrow-down class="w-4 h-4 text-orange-500" />
                        <span class="font-bold text-gray-800 dark:text-gray-100">Pied de page</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">(Footer global)</span>
                    </div>
                    <div x-show="step !== 4">
                        @if($footerBlockCount > 0)
                            <span class="text-xs bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full font-medium">{{ $footerBlockCount }} bloc{{ $footerBlockCount > 1 ? 's' : '' }}</span>
                        @else
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-400 px-2 py-0.5 rounded-full">Vide</span>
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0"
                     :class="step === 4 ? 'rotate-180' : ''"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>

            <div x-show="step === 4" class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-6 pb-6 pt-4">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Le footer est partagé sur toutes les pages du site. Son coût de programmation n'est compté qu'une seule fois.</p>
                @if($footerPage)
                    @include('livewire.partials.builder-page', ['page' => $footerPage, 'isGlobal' => true])
                @endif
                <div class="mt-4 flex justify-end">
                    <button @click="next(4)" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold text-sm flex items-center gap-2 transition-colors shadow-sm">
                        Continuer <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════
             ÉTAPE 5 : Options & Traduction
        ═══════════════════════════════════════════════════ --}}
        @php
            $user = auth()->user();
            $subscription = $user?->activeSubscription;
            $plan = $subscription?->plan;
            $canTranslate = $plan ? $plan->has_translation_module : true;
            $activeAddonCount = $estimation->addons->count();
        @endphp
        <div id="step-5" class="rounded-xl border-2 transition-all duration-200"
             :class="step === 5 ? 'border-blue-500 shadow-md' : 'border-gray-200 dark:border-gray-700'">

            <button @click="toggle(5)" class="w-full flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-black shrink-0"
                         :class="step === 5 ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-400'">
                        <span>5</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-fas-plus-circle class="w-4 h-4 text-purple-500" />
                        <span class="font-bold text-gray-800 dark:text-gray-100">Options & Traduction</span>
                        @if(!$canTranslate)
                            <span class="text-xs bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full font-bold flex items-center gap-1">
                                <x-fas-lock class="w-2 h-2" /> Pro
                            </span>
                        @endif
                    </div>
                    <div x-show="step !== 5" class="flex items-center gap-2">
                        @if($translation_enabled)
                            <span class="text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full font-medium">Traduction ON</span>
                        @endif
                        @if($activeAddonCount > 0)
                            <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">{{ $activeAddonCount }} add-on{{ $activeAddonCount > 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200 shrink-0"
                     :class="step === 5 ? 'rotate-180' : ''"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>

            <div x-show="step === 5" class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-6 pb-6 pt-4 space-y-6">
                {{-- Traduction --}}
                <div>
                    <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                        <x-fas-language class="w-4 h-4 text-purple-500" />
                        Traduction multilingue
                    </h3>
                    <div class="flex items-center space-x-2 mb-3">
                        <input type="checkbox" wire:model.live="translation_enabled" id="translation_enabled"
                               class="rounded text-blue-600 w-4 h-4" {{ !$canTranslate ? 'disabled' : '' }}>
                        <label for="translation_enabled" class="font-medium text-gray-700 dark:text-gray-300 {{ !$canTranslate ? 'opacity-50' : '' }}">Ce site sera traduit</label>
                    </div>

                    @if($translation_enabled)
                        <div class="ml-6 p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border-2 border-blue-200 dark:border-blue-800">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-blue-800 dark:text-blue-300 mb-3">Nombre de langues</label>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                                wire:click="$set('translation_languages_count', {{ max(1, $translation_languages_count - 1) }})"
                                                class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-blue-300 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors">
                                            <x-fas-minus class="w-3 h-3" />
                                        </button>
                                        <div class="w-14 h-10 bg-white dark:bg-gray-800 border-2 border-blue-400 rounded-lg flex items-center justify-center text-xl font-black text-blue-900 dark:text-blue-100">
                                            {{ $translation_languages_count }}
                                        </div>
                                        <button type="button"
                                                wire:click="$set('translation_languages_count', {{ $translation_languages_count + 1 }})"
                                                class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 border-2 border-blue-300 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors">
                                            <x-fas-plus class="w-3 h-3" />
                                        </button>
                                        @if($translation_languages_count > 1)
                                            <span class="text-xs font-bold text-orange-600 bg-orange-100 dark:bg-orange-900/40 dark:text-orange-300 px-2 py-1 rounded-full">Surcharge ×2</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    @if($type == 'fixed' && (!$isPriceLocked || $translation_fixed_price == 0))
                                        <div class="w-36">
                                            <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700 dark:text-blue-300">Coût forfaitaire</label>
                                            <div class="relative mt-1">
                                                <input type="number" step="0.01" wire:model.blur="translation_fixed_price"
                                                       class="block w-full border-2 border-blue-300 rounded-md text-sm p-2 pl-6 bg-white dark:bg-gray-800 dark:text-gray-100">
                                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-blue-400 font-bold pointer-events-none">{{ $currencySymbol }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if($type == 'hour' && (!$isHoursLocked || $translation_fixed_hours == 0))
                                        <div class="w-36">
                                            <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700 dark:text-blue-300">Heures fixes</label>
                                            <div class="relative mt-1">
                                                <input type="number" step="0.01" wire:model.blur="translation_fixed_hours"
                                                       class="block w-full border-2 border-blue-300 rounded-md text-sm p-2 pr-6 bg-white dark:bg-gray-800 dark:text-gray-100">
                                                <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-blue-400 font-bold pointer-events-none">h</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if(!$isPercentageLocked || $translation_percentage == 0)
                                        <div class="w-36">
                                            <label class="block text-[10px] uppercase tracking-wider font-black text-blue-700 dark:text-blue-300">Surcharge %</label>
                                            <div class="relative mt-1">
                                                <input type="number" step="0.01" wire:model.blur="translation_percentage"
                                                       class="block w-full border-2 border-blue-300 rounded-md text-sm p-2 pr-6 bg-white dark:bg-gray-800 dark:text-gray-100">
                                                <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-blue-400 font-bold pointer-events-none">%</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Add-ons --}}
                @if($addons->isNotEmpty())
                    <div>
                        <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                            <x-fas-puzzle-piece class="w-4 h-4 text-blue-500" />
                            Add-ons disponibles
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($addons as $addon)
                                <button wire:click="toggleAddon({{ $addon->id }})" type="button"
                                        class="cursor-pointer p-3 rounded-xl border-2 text-left transition-all {{ $estimation->addons->contains($addon->id) ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-300' }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold">{{ $addon->name }}</span>
                                        @if($estimation->addons->contains($addon->id))
                                            <x-fas-check class="w-4 h-4 text-white" />
                                        @endif
                                    </div>
                                    <div class="text-xs mt-0.5 {{ $estimation->addons->contains($addon->id) ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                        @if($addon->type == 'fixed_price') {{ $addon->value }} {{ $currencySymbol }}
                                        @elseif($addon->type == 'fixed_hours') {{ $addon->value }} h
                                        @else {{ $addon->value }}% ({{ $addon->calculation_base }})
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 text-gray-400 dark:text-gray-500">
                        <x-fas-puzzle-piece class="w-8 h-8 mx-auto mb-2 opacity-30" />
                        <p class="text-sm">Aucun add-on disponible pour ce type de projet.</p>
                        <a href="{{ route('settings.options') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 inline-block">Configurer les add-ons →</a>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════
         Sidebar : Récapitulatif
    ═══════════════════════════════════════════════════ --}}
    <div class="lg:col-span-1">
        <div class="bg-blue-900 text-white p-6 rounded-xl shadow-lg sticky top-8">
            <h2 class="text-lg font-bold mb-5 border-b border-blue-800 pb-3 text-center flex items-center justify-center gap-2">
                <x-fas-calculator class="w-5 h-5" />
                Récapitulatif
            </h2>

            <div class="space-y-2 text-sm">
                @foreach([
                    ['Base Technique', $totals['setup']],
                    ['Programmation', $totals['programming']],
                    ['Intégration', $totals['integration']],
                    ['Création Champs', $totals['field_creation']],
                    ['Gestion Contenu', $totals['content_management']],
                ] as [$label, $value])
                    <div class="flex justify-between items-center py-1.5 border-b border-blue-800/60">
                        <span class="text-blue-300">{{ $label }}</span>
                        <span class="font-semibold tabular-nums">{{ number_format($value, 2) }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-1.5 border-b border-blue-800/60 text-purple-300">
                    <span>Traduction</span>
                    <span class="font-semibold tabular-nums">{{ number_format($totals['translation'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-blue-800/60 text-blue-300">
                    <span>Add-ons</span>
                    <span class="font-semibold tabular-nums">{{ number_format($totals['addons'], 2) }}</span>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t-2 border-blue-700">
                @if($estimation->type == 'hour')
                    <div class="flex justify-between text-base font-bold mb-1">
                        <span>Total Temps</span>
                        <span class="tabular-nums">{{ number_format($totals['total_time'], 2) }} h</span>
                    </div>
                    @if($estimation->hourly_rate)
                        <div class="flex justify-between text-2xl font-black text-green-400 mt-2">
                            <span>Total Prix</span>
                            <span class="tabular-nums">{{ number_format($totals['total_price'], 2) }} {{ $currencySymbol }}</span>
                        </div>
                    @endif
                @else
                    <div class="flex justify-between text-2xl font-black text-green-400">
                        <span>Total</span>
                        <span class="tabular-nums">{{ number_format($totals['total_price'], 2) }} {{ $currencySymbol }}</span>
                    </div>
                @endif
            </div>

            @if((isset($totals['total_price']) && $totals['total_price'] > 0) || (isset($totals['total_time']) && $totals['total_time'] > 0 && $type === 'hour'))
                <a href="{{ route('estimations.pdf', $estimation) }}" target="_blank"
                   class="w-full block text-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg mt-6 transition-colors flex items-center justify-center gap-2">
                    <x-fas-file-pdf class="w-5 h-5" />
                    Générer le PDF
                </a>
            @endif
        </div>
    </div>

    {{-- Modal : Sauvegarder comme gabarit --}}
    <div x-data="{ show: false, templateName: '' }"
         @open-template-modal.window="show = true; templateName = ''"
         x-show="show" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-md mx-4" @click.stop>
            <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <x-fas-layer-group class="w-5 h-5 text-purple-600" />
                Sauvegarder comme gabarit
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">La structure (pages, blocs, add-ons) sera copiée dans un gabarit réutilisable.</p>
            <form method="POST" action="{{ route('estimations.save-as-template', $estimation) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom du gabarit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="templateName" required placeholder="ex: Landing Page, E-commerce..."
                           class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-purple-500">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="show = false"
                            class="px-4 py-2 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">Annuler</button>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 font-bold transition-colors">Créer le gabarit</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal : Nouveau bloc --}}
    @if($showBlockModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-xl w-full max-w-lg mx-4">
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
                                class="px-4 py-2 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">Annuler</button>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold transition-colors">Créer le Bloc</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal : Nouvelle Base Technique --}}
    <div x-data="{ open: @js($showSetupModal) }" x-show="open" x-on:setup-created.window="open = false"
         class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl sm:max-w-lg w-full relative">
                <form wire:submit.prevent="createSetup">
                    <div class="px-6 pt-6 pb-4">
                        <h3 class="text-lg font-bold text-blue-800 dark:text-blue-400 border-b dark:border-gray-700 pb-3 mb-4">✨ Ajouter une nouvelle base technique</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Nom / Type de base</label>
                                <input type="text" wire:model="newSetup.type"
                                       class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                       placeholder="ex: Refonte complète, Maintenance...">
                                @error('newSetup.type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Technologie</label>
                                <select wire:model="newSetup.project_type_id"
                                        class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="">Générique</option>
                                    @foreach($projectTypes as $pt)
                                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 rounded-b-xl flex justify-end gap-3">
                        <button type="button" wire:click="$set('showSetupModal', false)" @click="open = false"
                                class="px-4 py-2 border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition-colors">Annuler</button>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold transition-colors">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
