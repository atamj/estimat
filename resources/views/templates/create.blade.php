<x-layout>
    <x-slot:title>Nouveau Gabarit</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-md border-t-4 border-purple-600">
            <h1 class="text-2xl font-bold mb-8 text-gray-800 dark:text-gray-100 flex items-center">
                <x-fas-layer-group class="w-6 h-6 mr-3 text-purple-600" />
                Nouveau Gabarit
            </h1>

            <form action="{{ route('templates.store') }}" method="POST" class="space-y-8">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nom du gabarit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required autofocus value="{{ old('name') }}"
                           class="block w-full border-2 border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200 p-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                           placeholder="ex: Landing Page, E-commerce, Site Vitrine...">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Technologie / Type de Projet</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30 transition-all">
                            <input type="radio" name="project_type_id" value="" class="sr-only" {{ !collect($projectTypes)->contains('is_default', true) ? 'checked' : '' }}>
                            <span class="flex flex-col items-center justify-center w-full">
                                <x-fas-question class="w-6 h-6 mb-2 text-gray-400" />
                                <span class="text-xs font-bold text-gray-900 dark:text-gray-100 text-center">Générique</span>
                            </span>
                        </label>

                        @foreach($projectTypes as $pt)
                            <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30 transition-all">
                                <input type="radio" name="project_type_id" value="{{ $pt->id }}" class="sr-only" {{ $pt->is_default ? 'checked' : '' }}>
                                <span class="flex flex-col items-center justify-center w-full">
                                    @if($pt->icon)
                                        <x-dynamic-component :component="$pt->icon" class="w-6 h-6 mb-2 text-purple-600" />
                                    @else
                                        <x-fas-code class="w-6 h-6 mb-2 text-purple-600" />
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
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-4 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30">
                            <input type="radio" name="type" value="hour" class="sr-only" checked>
                            <span class="flex flex-col">
                                <span class="block text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                    <x-fas-clock class="w-4 h-4 mr-2 text-purple-600" />
                                    À l'heure
                                </span>
                                <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">Calcul basé sur le temps passé</span>
                            </span>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-4 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30">
                            <input type="radio" name="type" value="fixed" class="sr-only">
                            <span class="flex flex-col">
                                <span class="block text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                    <x-fas-euro-sign class="w-4 h-4 mr-2 text-purple-600" />
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
                            <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 dark:border-gray-600 p-3 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50 dark:has-[:checked]:bg-purple-900/30 transition-all">
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

                <div class="pt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('templates.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-sm font-medium">
                        Annuler
                    </a>
                    <button type="submit" class="bg-purple-600 text-white px-8 py-3 rounded-md hover:bg-purple-700 font-bold shadow-lg transition duration-200 flex items-center">
                        Créer le gabarit
                        <x-fas-arrow-right class="w-4 h-4 ml-2" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
