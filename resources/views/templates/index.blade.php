<x-layout>
    <x-slot:title>Mes Gabarits</x-slot:title>

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-black text-purple-900 dark:text-gray-100">Mes Gabarits</h1>
        <a href="{{ route('templates.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 flex items-center">
            <x-fas-plus class="w-4 h-4 mr-2" />
            Nouveau Gabarit
        </a>
    </div>

    @if($templates->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center border-t-4 border-purple-600">
            <x-fas-layer-group class="w-16 h-16 mx-auto text-purple-200 dark:text-purple-800 mb-4" />
            <h2 class="text-xl font-bold text-gray-600 dark:text-gray-300 mb-2">Aucun gabarit</h2>
            <p class="text-gray-400 dark:text-gray-500 mb-6">Créez des gabarits réutilisables pour démarrer vos estimations plus vite.</p>
            <a href="{{ route('templates.create') }}" class="bg-purple-600 text-white px-6 py-3 rounded-md hover:bg-purple-700 font-bold inline-flex items-center">
                <x-fas-plus class="w-4 h-4 mr-2" />
                Créer mon premier gabarit
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $template)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-t-4 border-purple-600 flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex items-start justify-between mb-3">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 leading-tight">{{ $template->name }}</h2>
                            <span class="ml-2 shrink-0 text-xs font-bold px-2 py-0.5 rounded-full {{ $template->type === 'hour' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $template->type === 'hour' ? 'À l\'heure' : 'Forfait' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400 mb-4">
                            @if($template->projectType)
                                <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                    <x-fas-microchip class="w-3 h-3" />
                                    {{ $template->projectType->name }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                <x-fas-file-alt class="w-3 h-3" />
                                {{ $template->regularPages->count() }} page(s)
                            </span>
                            <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                <x-fas-cubes class="w-3 h-3" />
                                {{ $template->pages->flatMap->blocks->count() }} bloc(s)
                            </span>
                        </div>
                    </div>

                    <div class="px-6 pb-6 flex flex-wrap gap-2">
                        <a href="{{ route('templates.builder', $template) }}" class="flex-1 text-center bg-purple-600 text-white px-3 py-2 rounded-md hover:bg-purple-700 text-sm font-bold transition-colors">
                            <x-fas-edit class="w-3 h-3 inline mr-1" />
                            Modifier
                        </a>

                        <form method="POST" action="{{ route('templates.create-estimation', $template) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 text-sm font-bold transition-colors">
                                <x-fas-file-invoice class="w-3 h-3 inline mr-1" />
                                Créer estimation
                            </button>
                        </form>

                        <form method="POST" action="{{ route('templates.duplicate', $template) }}">
                            @csrf
                            <button type="submit" class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-bold transition-colors" title="Dupliquer">
                                <x-fas-copy class="w-3 h-3" />
                            </button>
                        </form>

                        <form method="POST" action="{{ route('templates.destroy', $template) }}" onsubmit="return confirm('Supprimer ce gabarit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-3 py-2 rounded-md hover:bg-red-200 dark:hover:bg-red-900/50 text-sm font-bold transition-colors" title="Supprimer">
                                <x-fas-trash-alt class="w-3 h-3" />
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layout>
