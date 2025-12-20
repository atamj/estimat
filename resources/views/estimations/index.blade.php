<x-layout>
    <x-slot:title>Mes Estimations</x-slot:title>

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Mes Estimations</h1>
        <a href="{{ route('estimations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
            <x-fas-plus class="w-4 h-4 mr-2" />
            Nouvelle Estimation
        </a>
    </div>

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
                    <tr>
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
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end space-x-4">
                            <a href="{{ route('estimations.builder', $estimation) }}" class="text-blue-600 hover:text-blue-900 flex items-center" title="Modifier">
                                <x-fas-edit class="w-4 h-4 mr-1" />
                                Modifier
                            </a>
                            @if($estimation->has_content)
                                <a href="{{ route('estimations.pdf', $estimation) }}" target="_blank" class="text-green-600 hover:text-green-900 flex items-center" title="PDF">
                                    <x-fas-file-pdf class="w-4 h-4 mr-1" />
                                    PDF
                                </a>
                            @endif
                            <form action="{{ route('estimations.destroy', $estimation) }}" method="POST" onsubmit="return confirm('Supprimer cette estimation ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center" title="Supprimer">
                                    <x-fas-trash class="w-4 h-4 mr-1" />
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                            Aucune estimation pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
