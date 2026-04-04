<x-layout>
    <x-slot:title>Mes Estimations</x-slot:title>

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-black text-blue-900 dark:text-gray-100">Mes Estimations</h1>
        <a href="{{ route('estimations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
            <x-fas-plus class="w-4 h-4 mr-2" />
            Nouvelle Estimation
        </a>
    </div>

    <livewire:estimation-list />
</x-layout>
