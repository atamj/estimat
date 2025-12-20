<x-layout>
    <x-slot:title>Nouvelle Estimation - Étape 2</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white p-8 rounded-lg shadow-md border-t-4 border-blue-600">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <x-fas-user class="w-6 h-6 mr-3 text-blue-600" />
                    Informations Client
                </h1>
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full">Étape 2 / 2</span>
            </div>

            <form action="{{ route('estimations.store') }}" method="POST" class="space-y-6" x-data="{ showProject: false }">
                @csrf
                <input type="hidden" name="project_type_id" value="{{ $project_type_id }}">
                <input type="hidden" name="type" value="{{ $type }}">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom du client (obligatoire)</label>
                    <input type="text" name="client_name" required autofocus
                           class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-3"
                           placeholder="ex: Jean Dupont, Société ACME...">
                    @error('client_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div x-show="!showProject" class="pt-2">
                    <button type="button" @click="showProject = true" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center transition-colors">
                        <x-fas-plus-circle class="w-4 h-4 mr-2" />
                        Ajouter un projet
                    </button>
                </div>

                <div x-show="showProject" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nom du projet (optionnel)</label>
                    <input type="text" name="project_name"
                           class="mt-1 block w-full border-2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 p-3"
                           placeholder="ex: Refonte Site Vitrine, Application Mobile...">
                    @error('project_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="pt-6 flex items-center justify-between border-t border-gray-100">
                    <a href="{{ url()->previous() }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium flex items-center">
                        <x-fas-arrow-left class="w-3 h-3 mr-2" />
                        Retour
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 font-bold shadow-lg transition duration-200 flex items-center">
                        Créer l'estimation
                        <x-fas-check class="w-4 h-4 ml-2" />
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
