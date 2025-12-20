<x-layout>
    <x-slot:title>Builder d'Estimation</x-slot:title>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Builder d'Estimation</h1>
        <a href="{{ route('estimations.index') }}" class="text-blue-600 hover:underline">Retour à la liste</a>
    </div>
    <livewire:estimation-builder :estimation="$estimation" />
</x-layout>
