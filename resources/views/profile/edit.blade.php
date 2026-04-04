<x-layout>
    <x-slot:title>Mon Compte</x-slot:title>

    <div class="max-w-3xl mx-auto space-y-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-gray-100">Mon Compte</h1>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700">
            @include('profile.partials.update-password-form')
        </div>

        <div id="subscription" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700">
            @livewire('subscription-manager')
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-layout>
