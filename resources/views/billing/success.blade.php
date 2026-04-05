<x-layout>
    <x-slot:title>Paiement confirmé</x-slot:title>

    <div class="max-w-lg mx-auto text-center py-16">
        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <x-fas-check class="w-10 h-10 text-green-500" />
        </div>

        <h1 class="text-3xl font-black text-gray-900 dark:text-gray-100 mb-3">Paiement réussi !</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            Votre abonnement est en cours d'activation. Cela peut prendre quelques secondes.
            Vous recevrez une confirmation par email.
        </p>

        <a href="{{ route('subscription') }}"
           class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
            <x-fas-arrow-right class="w-4 h-4" />
            Voir mon abonnement
        </a>
    </div>
</x-layout>
