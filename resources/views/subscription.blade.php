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

    @if(session('pending_checkout'))
        @php
            /** @var array{plan_id: int, billing_cycle: string} $pending */
            $pending = session('pending_checkout');
        @endphp
        <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center gap-4 bg-white/95 dark:bg-gray-900/95 px-6 text-center">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Redirection vers le paiement sécurisé Stripe…</p>
        </div>
        <form id="pending-checkout-form" method="POST" action="{{ route('billing.checkout', ['plan' => $pending['plan_id']]) }}" class="hidden">
            @csrf
            <input type="hidden" name="billing_cycle" value="{{ $pending['billing_cycle'] }}" />
        </form>
        <script>
            document.getElementById('pending-checkout-form').submit();
        </script>
    @endif
</x-layout>
