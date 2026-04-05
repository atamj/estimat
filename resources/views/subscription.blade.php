<x-layout>
    <x-slot:title>Mon Abonnement</x-slot:title>

    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-6 py-4 flex items-center gap-3">
            <x-fas-check-circle class="w-5 h-5 text-green-500 shrink-0" />
            <p class="text-green-800 dark:text-green-300 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-6 py-4 flex items-center gap-3">
            <x-fas-times-circle class="w-5 h-5 text-red-500 shrink-0" />
            <p class="text-red-800 dark:text-red-300 text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <livewire:subscription-manager />
</x-layout>
