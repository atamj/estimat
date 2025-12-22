<div>
    <div class="mb-6 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <x-fas-search class="h-5 w-5 text-gray-400" />
        </div>
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Rechercher par client ou projet..."
            class="block w-full pl-10 pr-3 py-2 border-2 border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-500 sm:text-sm"
        >
        @if($search)
            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <x-fas-times class="h-4 w-4 text-gray-400 hover:text-gray-600" />
            </button>
        @endif
    </div>

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client / Projet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($estimations as $estimation)
                    <tr wire:key="estimation-{{ $estimation->id }}">
                        <td class="px-6 py-4 whitespace-nowrap flex items-center">
                            @if($estimation->projectType && $estimation->projectType->icon)
                                <x-dynamic-component :component="$estimation->projectType->icon" class="w-8 h-8 mr-4 text-blue-900 bg-blue-50 p-1.5 rounded-lg shadow-sm border border-blue-100" />
                            @else
                                <div class="w-8 h-8 mr-4 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-400">
                                    <x-fas-question class="w-4 h-4" />
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $estimation->client_name }}</div>
                                @if($estimation->project_name)
                                    <div class="text-sm text-gray-500">{{ $estimation->project_name }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $estimation->type == 'hour' ? 'À l\'heure' : 'Forfait' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $estimation->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end items-center space-x-4">
                            @if($estimation->total_price > 0 || ($estimation->type == 'hour' && $estimation->total_time > 0))
                                <div class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">
                                    {{ $estimation->type == 'hour' ? number_format($estimation->total_time, 1).'h' : number_format($estimation->total_price, 2).'€' }}
                                </div>
                            @endif

                            <a href="{{ route('estimations.builder', $estimation) }}" class="text-blue-600 hover:text-blue-900 flex items-center" title="Modifier">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                <span class="hidden md:inline">Modifier</span>
                            </a>

                            @if($estimation->has_content)
                                <a href="{{ route('estimations.pdf', $estimation) }}" target="_blank" class="text-green-600 hover:text-green-900 flex items-center" title="PDF">
                                    <x-fas-file-pdf class="w-4 h-4 mr-1" />
                                    <span class="hidden md:inline">PDF</span>
                                </a>
                            @endif

                            <button wire:click="duplicate({{ $estimation->id }})" class="text-gray-600 hover:text-gray-900 flex items-center" title="Dupliquer">
                                <x-fas-copy class="w-4 h-4 mr-1" />
                                <span class="hidden md:inline">Dupliquer</span>
                            </button>

                            <button wire:click="delete({{ $estimation->id }})" wire:confirm="Supprimer cette estimation ?" class="text-red-600 hover:text-red-900 flex items-center" title="Supprimer">
                                <x-fas-trash class="w-4 h-4 mr-1" />
                                <span class="hidden md:inline">Supprimer</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Aucune estimation trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
