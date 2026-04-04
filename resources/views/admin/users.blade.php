<x-layout>
    <x-slot:title>Gestion des Utilisateurs</x-slot:title>

    <div class="max-w-7xl mx-auto py-10">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-gray-100 mb-8 flex items-center gap-3">
            <x-fas-users class="w-8 h-8 text-blue-600" />
            Gestion des Comptes
        </h1>

        <livewire:admin.user-manager />
    </div>
</x-layout>
