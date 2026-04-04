<x-layout>
    <x-slot:title>Builder de Gabarit — {{ $template->name }}</x-slot:title>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold dark:text-gray-100">
            <span class="text-purple-700">Gabarit :</span> {{ $template->name }}
        </h1>
        <a href="{{ route('templates.index') }}" class="text-purple-600 hover:underline">Retour aux gabarits</a>
    </div>
    <livewire:template-builder :template="$template" />
</x-layout>
