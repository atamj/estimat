<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Bandeau d'infos -->
        <div class="bg-purple-900 text-white p-4 rounded-lg shadow-md flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-6">
                <!-- Technologie -->
                <div class="flex items-center">
                    @if($project_type_id)
                        @php $pt = \App\Models\ProjectType::find($project_type_id); @endphp
                        @if($pt && $pt->icon)
                            <div class="bg-white p-2 rounded-lg shadow-sm mr-3">
                                <x-dynamic-component :component="$pt->icon" class="w-6 h-6 text-purple-900" />
                            </div>
                        @endif
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Technologie</p>
                            <p class="font-bold text-lg">{{ $pt ? $pt->name : 'Générique' }}</p>
                        </div>
                    @else
                        <div class="bg-purple-800 p-2 rounded-lg shadow-sm mr-3">
                            <x-fas-question class="w-6 h-6 text-purple-300" />
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Technologie</p>
                            <p class="font-bold text-lg italic">Générique</p>
                        </div>
                    @endif
                </div>

                <!-- Séparateur -->
                <div class="hidden md:block h-10 w-px bg-purple-700"></div>

                <!-- Type -->
                <div class="flex items-center">
                    <div class="bg-purple-800 p-2 rounded-lg shadow-sm mr-3">
                        @if($type == 'hour')
                            <x-fas-clock class="w-6 h-6 text-purple-300" />
                        @else
                            <x-fas-euro-sign class="w-6 h-6 text-purple-300" />
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Mode de facturation</p>
                        <p class="font-bold text-lg">{{ $type == 'hour' ? 'À l\'heure' : 'Forfaitaire' }}</p>
                    </div>
                </div>

                <!-- Séparateur -->
                <div class="hidden md:block h-10 w-px bg-purple-700"></div>

                <!-- Stats -->
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Pages</p>
                        <p class="font-bold text-lg">{{ $template->regularPages->count() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] uppercase tracking-widest text-purple-300 font-bold">Blocs</p>
                        <p class="font-bold text-lg">{{ $template->pages->flatMap->blocks->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration du Gabarit -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-purple-800 dark:text-purple-400 border-b dark:border-gray-700 pb-2 flex items-center">
                <x-fas-cog class="w-5 h-5 mr-2" />
                1. Configuration du Gabarit
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div x-data="{ isEditing: false }">
                    <label class="block text-sm font-bold text-gray-700">Nom du gabarit</label>
                    <div class="mt-1 flex items-center justify-between group h-10">
                        <div x-show="!isEditing" class="flex items-center space-x-2">
                            <span class="text-lg font-medium text-gray-900">{{ $name }}</span>
                            <button @click="isEditing = true" class="text-gray-400 hover:text-purple-600 transition-colors p-1" title="Modifier le nom">
                                <x-fas-edit class="w-4 h-4" />
                            </button>
                        </div>
                        <div x-show="isEditing" @click.away="isEditing = false" class="w-full" x-cloak>
                            <input type="text"
                                   wire:model.blur="name"
                                   @keydown.enter="isEditing = false"
                                   class="block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 p-2"
                                   x-ref="nameInput"
                                   x-init="$watch('isEditing', value => { if(value) { setTimeout(() => $refs.nameInput.focus(), 50) } })">
                        </div>
                    </div>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">Base technique & Mise en place</label>
                    <select wire:change="handleSetupSelection($event.target.value)" class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 p-2">
                        <option value="">Aucune base</option>
                        @foreach($setups as $setup)
                            <option value="{{ $setup->id }}" {{ $setup_id == $setup->id ? 'selected' : '' }}>{{ $setup->type }}</option>
                        @endforeach
                        <option value="new_setup" class="font-bold text-green-600">✨ Ajouter une nouvelle base technique...</option>
                    </select>

                    @if($setup_id)
                        @php $currentSetup = \App\Models\Setup::find($setup_id); @endphp
                        @if($currentSetup)
                            <div class="mt-2 p-4 bg-purple-50 rounded-xl border-2 border-purple-200 shadow-inner space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-purple-800 text-xs font-bold uppercase tracking-wider">
                                        <x-fas-info-circle class="w-4 h-4 mr-2" />
                                        Détails de la base technique
                                    </div>
                                    <button type="button" wire:click="toggleSetupEditing" class="text-purple-600 hover:text-purple-800 text-[10px] font-bold uppercase flex items-center">
                                        <x-fas-edit class="w-3 h-3 mr-1" />
                                        {{ $isSetupEditing ? 'Terminer l\'édition' : 'Modifier' }}
                                    </button>
                                </div>
                                @if($type == 'hour')
                                <div class="bg-white p-3 rounded-lg border-2 border-purple-500 shadow-sm">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-purple-700">Nombre d'heures</label>
                                    @if($currentSetup->fixed_hours > 0 && !$isSetupEditing)
                                        <div class="text-lg font-black text-purple-900">{{ number_format($currentSetup->fixed_hours, 2) }} h</div>
                                    @else
                                        <div class="relative mt-1">
                                            <input type="number" step="0.01"
                                                   value="{{ $currentSetup->fixed_hours }}"
                                                   onchange="@this.updateSetupValue({{ $currentSetup->id }}, 'fixed_hours', this.value)"
                                                   class="block w-full border-2 border-purple-300 rounded-md shadow-sm text-sm p-2 pr-7 focus:border-purple-500 bg-white">
                                            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-purple-400 font-bold">h</div>
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Structure du site -->
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-purple-800 dark:text-purple-400 flex items-center">
                    <x-fas-layer-group class="w-5 h-5 mr-2" />
                    2. Structure du site
                </h2>
                <button wire:click="addPage" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm flex items-center">
                    <x-fas-plus class="w-3 h-3 mr-2" />
                    Ajouter une page
                </button>
            </div>

            <!-- Site Header -->
            @if($template->headerPage)
                @include('livewire.partials.template-builder-page', ['page' => $template->headerPage, 'isGlobal' => true])
            @endif

            <!-- Regular Pages -->
            @foreach($template->regularPages as $page)
                @include('livewire.partials.template-builder-page', ['page' => $page, 'isGlobal' => false])
            @endforeach

            <!-- Site Footer -->
            @if($template->footerPage)
                @include('livewire.partials.template-builder-page', ['page' => $template->footerPage, 'isGlobal' => true])
            @endif
        </div>

        <!-- Options globales -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-purple-800 dark:text-purple-400 border-b dark:border-gray-700 pb-2 flex items-center justify-between">
                <div class="flex items-center">
                    <x-fas-plus-circle class="w-5 h-5 mr-2" />
                    3. Options & Traduction
                </div>
            </h2>

            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" wire:model.live="translation_enabled" id="translation_enabled" class="rounded text-purple-600">
                    <label for="translation_enabled" class="font-medium text-gray-700">Ce gabarit inclut la traduction</label>
                </div>

                @if($translation_enabled)
                    <div class="ml-6 mt-4 p-6 bg-purple-50 rounded-xl border-2 border-purple-200 shadow-inner">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-purple-800 mb-3 flex items-center">
                                    <x-fas-language class="w-4 h-4 mr-2" />
                                    Nombre de langues du projet
                                </label>
                                <div class="flex items-center space-x-3">
                                    <button type="button"
                                            wire:click="$set('translation_languages_count', {{ max(1, $translation_languages_count - 1) }})"
                                            class="w-10 h-10 rounded-full bg-white border-2 border-purple-300 text-purple-600 flex items-center justify-center hover:bg-purple-100 transition-colors shadow-sm">
                                        <x-fas-minus class="w-3 h-3" />
                                    </button>
                                    <div class="w-16 h-12 bg-white border-2 border-purple-400 rounded-lg flex items-center justify-center text-xl font-black text-purple-900 shadow-sm">
                                        {{ $translation_languages_count }}
                                    </div>
                                    <button type="button"
                                            wire:click="$set('translation_languages_count', {{ $translation_languages_count + 1 }})"
                                            class="w-10 h-10 rounded-full bg-white border-2 border-purple-300 text-purple-600 flex items-center justify-center hover:bg-purple-100 transition-colors shadow-sm">
                                        <x-fas-plus class="w-3 h-3" />
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-4">
                                @if($type == 'fixed')
                                <div class="w-40">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-purple-700">Coût Forfaitaire</label>
                                    <div class="relative mt-1">
                                        <input type="number" step="0.01" wire:model.blur="translation_fixed_price"
                                               class="block w-full border-2 border-purple-300 rounded-md shadow-sm text-sm p-2 pl-7 focus:border-purple-500 bg-white">
                                        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none text-purple-400 font-bold">{{ $currencySymbol }}</div>
                                    </div>
                                </div>
                                @endif

                                @if($type == 'hour')
                                <div class="w-40">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-purple-700">Heures Fixes</label>
                                    <div class="relative mt-1">
                                        <input type="number" step="0.01" wire:model.blur="translation_fixed_hours"
                                               class="block w-full border-2 border-purple-300 rounded-md shadow-sm text-sm p-2 pr-7 focus:border-purple-500 bg-white">
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-purple-400 font-bold">h</div>
                                    </div>
                                </div>
                                @endif

                                <div class="w-40">
                                    <label class="block text-[10px] uppercase tracking-wider font-black text-purple-700">Surcharge %</label>
                                    <div class="relative mt-1">
                                        <input type="number" step="0.01" wire:model.blur="translation_percentage"
                                               class="block w-full border-2 border-purple-300 rounded-md shadow-sm text-sm p-2 pr-7 focus:border-purple-500 bg-white">
                                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-purple-400 font-bold">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6">
                    <label class="block font-medium text-gray-700 mb-2">Add-ons disponibles</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($addons as $addon)
                            <div wire:click="toggleAddon({{ $addon->id }})"
                                 class="cursor-pointer p-3 rounded-md border {{ $template->addons->contains($addon->id) ? 'bg-purple-600 text-white border-purple-600' : 'bg-gray-50 border-gray-200 text-gray-700' }}">
                                <div class="text-sm font-bold">{{ $addon->name }}</div>
                                <div class="text-xs {{ $template->addons->contains($addon->id) ? 'text-purple-100' : 'text-gray-500' }}">
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

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-purple-900 text-white p-6 rounded-lg shadow-lg sticky top-8">
            <h2 class="text-xl font-bold mb-6 border-b border-purple-800 pb-2 text-center flex items-center justify-center">
                <x-fas-layer-group class="w-5 h-5 mr-2" />
                Gabarit
            </h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-purple-800 pb-1">
                    <span>Pages régulières :</span>
                    <span class="font-medium">{{ $template->regularPages->count() }}</span>
                </div>
                <div class="flex justify-between border-b border-purple-800 pb-1">
                    <span>Total blocs :</span>
                    <span class="font-medium">{{ $template->pages->flatMap->blocks->count() }}</span>
                </div>
                <div class="flex justify-between border-b border-purple-800 pb-1">
                    <span>Add-ons :</span>
                    <span class="font-medium">{{ $template->addons->count() }}</span>
                </div>
                <div class="flex justify-between border-b border-purple-800 pb-1">
                    <span>Type :</span>
                    <span class="font-medium">{{ $type == 'hour' ? 'À l\'heure' : 'Forfaitaire' }}</span>
                </div>
                @if($template->setup)
                <div class="flex justify-between border-b border-purple-800 pb-1">
                    <span>Base technique :</span>
                    <span class="font-medium text-xs">{{ $template->setup->type }}</span>
                </div>
                @endif
            </div>

            <div class="mt-8 p-4 bg-purple-800 rounded-lg text-center">
                <p class="text-purple-300 text-xs">Les gabarits sont des modèles réutilisables sans calcul de prix.</p>
            </div>
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
                               class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-purple-500">
                        @error('newBlock.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Technologie</label>
                        <select wire:model="newBlock.project_type_id"
                                class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg p-2.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-purple-500">
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
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 font-bold transition-colors">
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
                        <h3 class="text-lg font-bold text-purple-800 dark:text-purple-400 border-b dark:border-gray-700 pb-2 mb-4">✨ Ajouter une nouvelle base technique</h3>
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
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-bold text-white hover:bg-purple-700 sm:ml-3 sm:w-auto sm:text-sm">
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
