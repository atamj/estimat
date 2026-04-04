<x-layout>
    <x-slot:title>Nouvelle Estimation</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6" x-data="{ selectedTemplate: null, selectedTemplateName: '' }">
        @if($templates->isNotEmpty())
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md border-t-4 border-purple-600">
                <h2 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-100 flex items-center">
                    <x-fas-layer-group class="w-5 h-5 mr-2 text-purple-600" />
                    Partir d'un gabarit
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($templates as $template)
                        <button type="button"
                            @click="selectedTemplate = selectedTemplate === {{ $template->id }} ? null : {{ $template->id }}; selectedTemplateName = '{{ addslashes($template->name) }}'"
                            :class="selectedTemplate === {{ $template->id }} ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30' : 'border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20'"
                            class="w-full text-left p-4 rounded-lg border-2 transition-all group relative">
                            <div class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-purple-700 dark:group-hover:text-purple-300">{{ $template->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-3">
                                @if($template->projectType)
                                    <span>{{ $template->projectType->name }}</span>
                                @endif
                                <span>{{ $template->regularPages->count() }} page(s)</span>
                                <span>{{ $template->pages->flatMap->blocks->count() }} bloc(s)</span>
                            </div>
                            <x-fas-check x-show="selectedTemplate === {{ $template->id }}" class="absolute top-3 right-3 w-4 h-4 text-purple-600" />
                        </button>
                    @endforeach
                </div>
                <p class="mt-3 text-xs text-gray-400 dark:text-gray-500 italic">
                    Sélectionnez un gabarit pour pré-remplir la structure de la nouvelle estimation.
                    <a href="{{ route('templates.index') }}" class="text-purple-600 hover:underline ml-1">Gérer les gabarits</a>
                </p>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md border-t-4 border-blue-600">
            <h1 class="text-2xl font-bold mb-8 text-gray-800 dark:text-gray-100 flex items-center">
                <x-fas-file-invoice class="w-6 h-6 mr-3 text-blue-600" />
                Nouvelle Estimation
            </h1>

            <div x-show="selectedTemplate" x-cloak x-transition class="mb-6 flex items-center gap-2 text-sm text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg px-4 py-3">
                <x-fas-layer-group class="w-4 h-4 shrink-0" />
                <span>Gabarit sélectionné : <strong x-text="selectedTemplateName"></strong></span>
                <button type="button" @click="selectedTemplate = null" class="ml-auto text-purple-400 hover:text-purple-600">
                    <x-fas-times class="w-3 h-3" />
                </button>
            </div>

            <form action="{{ route('estimations.store') }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="template_id" :value="selectedTemplate">

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Technologie / Type de Projet</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30 transition-all">
                            <input type="radio" name="project_type_id" value="" class="sr-only" {{ !collect($projectTypes)->contains('is_default', true) ? 'checked' : '' }}>
                            <span class="flex flex-col items-center justify-center w-full">
                                <x-fas-question class="w-6 h-6 mb-2 text-gray-400" />
                                <span class="text-xs font-bold text-gray-900 dark:text-gray-100 text-center">Générique</span>
                            </span>
                        </label>

                        @foreach($projectTypes as $pt)
                            <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30 transition-all">
                                <input type="radio" name="project_type_id" value="{{ $pt->id }}" class="sr-only" {{ $pt->is_default ? 'checked' : '' }}>
                                <span class="flex flex-col items-center justify-center w-full">
                                    @if($pt->icon)
                                        <x-dynamic-component :component="$pt->icon" class="w-6 h-6 mb-2 text-blue-600" />
                                    @else
                                        <x-fas-code class="w-6 h-6 mb-2 text-blue-600" />
                                    @endif
                                    <span class="text-xs font-bold text-gray-900 dark:text-gray-100 text-center">{{ $pt->name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-[10px] text-gray-500 italic uppercase tracking-tighter">Cela filtrera les blocs et add-ons disponibles.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Type d'estimation</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-4 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30">
                            <input type="radio" name="type" value="hour" class="sr-only" checked>
                            <span class="flex flex-col">
                                <span class="block text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                    <x-fas-clock class="w-4 h-4 mr-2 text-blue-600" />
                                    À l'heure
                                </span>
                                <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">Calcul basé sur le temps passé</span>
                            </span>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-4 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30">
                            <input type="radio" name="type" value="fixed" class="sr-only">
                            <span class="flex flex-col">
                                <span class="block text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                    <x-fas-euro-sign class="w-4 h-4 mr-2 text-blue-600" />
                                    Forfaitaire
                                </span>
                                <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">Prix fixe global défini par bloc</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Devise</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach($currencies as $currency)
                            <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/30 transition-all">
                                <input type="radio" name="currency" value="{{ $currency->value }}" class="sr-only" {{ $currency->value === $defaultCurrency ? 'checked' : '' }}>
                                <span class="flex flex-col items-center justify-center w-full gap-0.5">
                                    <span class="text-lg font-black text-gray-700 dark:text-gray-200">{{ $currency->symbol() }}</span>
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase">{{ $currency->value }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('currency') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nom du client <span class="text-red-500">*</span></label>
                        <input type="text" name="client_name" required autofocus value="{{ old('client_name') }}"
                               class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                               placeholder="ex: Jean Dupont, Société ACME...">
                        @error('client_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <div x-show="!show">
                            <button type="button" @click="show = true" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center transition-colors">
                                <x-fas-plus-circle class="w-4 h-4 mr-2" />
                                Ajouter un nom de projet (optionnel)
                            </button>
                        </div>
                        <div x-show="show" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nom du projet</label>
                            <input type="text" name="project_name" value="{{ old('project_name') }}"
                                   class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                   placeholder="ex: Refonte Site Vitrine, Application Mobile...">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('estimations.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-sm font-medium">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 font-bold shadow-lg transition duration-200 flex items-center">
                        Créer l'estimation
                        <x-fas-arrow-right class="w-4 h-4 ml-2" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
