<x-layout>
    <x-slot:title>Nouvelle Estimation - Étape 1</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-lg shadow-md border-t-4 border-blue-600">
            <h1 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                <x-fas-file-invoice class="w-6 h-6 mr-3 text-blue-600" />
                Nouvelle Estimation
            </h1>

            <form action="{{ route('estimations.create.step2') }}" method="GET" class="space-y-6">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Technologie / Type de Projet</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 p-3 shadow-sm focus:outline-none hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all">
                            <input type="radio" name="project_type_id" value="" class="sr-only" checked>
                            <span class="flex flex-col items-center justify-center w-full">
                                <x-fas-question class="w-6 h-6 mb-2 text-gray-400" />
                                <span class="text-xs font-bold text-gray-900 text-center">Générique</span>
                            </span>
                        </label>

                        @foreach($projectTypes as $pt)
                            <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 p-3 shadow-sm focus:outline-none hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all">
                                <input type="radio" name="project_type_id" value="{{ $pt->id }}" class="sr-only">
                                <span class="flex flex-col items-center justify-center w-full">
                                    @if($pt->icon)
                                        <x-dynamic-component :component="$pt->icon" class="w-6 h-6 mb-2 text-blue-600" />
                                    @else
                                        <x-fas-code class="w-6 h-6 mb-2 text-blue-600" />
                                    @endif
                                    <span class="text-xs font-bold text-gray-900 text-center">{{ $pt->name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-[10px] text-gray-500 italic uppercase tracking-tighter">Cela filtrera les blocs et add-ons disponibles.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Type d'estimation</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 p-4 shadow-sm focus:outline-none hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="type" value="hour" class="sr-only" checked>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-bold text-gray-900 flex items-center">
                                        <x-fas-clock class="w-4 h-4 mr-2 text-blue-600" />
                                        À l'heure
                                    </span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">
                                        Calcul basé sur le temps passé
                                    </span>
                                </span>
                            </span>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border-2 border-gray-200 p-4 shadow-sm focus:outline-none hover:bg-gray-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="type" value="fixed" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-bold text-gray-900 flex items-center">
                                        <x-fas-euro-sign class="w-4 h-4 mr-2 text-blue-600" />
                                        Forfaitaire
                                    </span>
                                    <span class="mt-1 flex items-center text-xs text-gray-500">
                                        Prix fixe global défini par bloc
                                    </span>
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-gray-100">
                    <a href="{{ route('estimations.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                        Annuler
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 font-bold shadow-lg transition duration-200 flex items-center">
                        Continuer vers le Builder
                        <x-fas-arrow-right class="w-4 h-4 ml-2" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
