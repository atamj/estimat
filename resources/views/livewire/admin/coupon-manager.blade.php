<div class="space-y-8">
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-end">
        <button wire:click="toggleForm" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-200">
            <x-fas-plus class="w-4 h-4" />
            {{ $showForm ? 'Annuler' : 'Nouveau Code Promo' }}
        </button>
    </div>

    @if($showForm)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border-2 border-blue-100 dark:border-blue-900 shadow-sm animate-fadeIn">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 dark:text-gray-100">
                <x-fas-ticket-alt class="w-5 h-5 text-blue-600" />
                {{ $editingCouponId ? 'Modifier le Code Promo' : 'Créer un nouveau Code Promo' }}
            </h2>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Code</label>
                        <input type="text" wire:model="code" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition uppercase bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Ex: PIONNIER20">
                        @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Type</label>
                        <select wire:model="type" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            <option value="percentage">Pourcentage (%)</option>
                            <option value="fixed">Montant fixe (€)</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Valeur</label>
                        <input type="number" step="0.01" wire:model="value" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        @error('value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Date d'expiration</label>
                        <input type="datetime-local" wire:model="expires_at" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-gray-300 mb-2">Nombre max d'utilisations</label>
                        <input type="number" wire:model="max_uses" class="w-full p-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200 outline-none transition bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" placeholder="Illimité si vide">
                    </div>
                </div>

                <div class="flex items-center gap-6 border-t pt-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" wire:model="is_active" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 transition">
                        <span class="text-sm font-bold text-slate-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">Code Actif</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" wire:click="toggleForm" class="px-6 py-2 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-slate-600 dark:text-gray-300 font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Annuler
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                        {{ $editingCouponId ? 'Mettre à jour' : 'Enregistrer le Code Promo' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-slate-200 dark:border-gray-700 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-gray-900 border-b-2 border-slate-200 dark:border-gray-700">
                    <th class="p-4 text-sm font-bold text-slate-600 dark:text-gray-400 uppercase tracking-wider">Code</th>
                    <th class="p-4 text-sm font-bold text-slate-600 dark:text-gray-400 uppercase tracking-wider">Réduction</th>
                    <th class="p-4 text-sm font-bold text-slate-600 dark:text-gray-400 uppercase tracking-wider">Validité</th>
                    <th class="p-4 text-sm font-bold text-slate-600 dark:text-gray-400 uppercase tracking-wider">Utilisations</th>
                    <th class="p-4 text-sm font-bold text-slate-600 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-gray-700/50 transition">
                        <td class="p-4">
                            <span class="font-mono font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">{{ $coupon->code }}</span>
                            @if(!$coupon->is_active)
                                <span class="ml-2 text-[10px] text-red-500 font-bold uppercase">Inactif</span>
                            @endif
                        </td>
                        <td class="p-4 text-sm font-medium text-slate-700 dark:text-gray-300">
                            {{ $coupon->type === 'percentage' ? $coupon->value . '%' : number_format($coupon->value, 2) . '€' }}
                        </td>
                        <td class="p-4 text-sm text-slate-500 dark:text-gray-400">
                            @if($coupon->expires_at)
                                Jusqu'au {{ $coupon->expires_at->format('d/m/Y H:i') }}
                                @if($coupon->expires_at->isPast())
                                    <span class="text-red-500 font-bold">(Expiré)</span>
                                @endif
                            @else
                                Sans limite
                            @endif
                        </td>
                        <td class="p-4 text-sm">
                            <span class="font-bold text-slate-700 dark:text-gray-300">{{ $coupon->uses_count }}</span>
                            @if($coupon->max_uses)
                                <span class="text-slate-400 dark:text-gray-500">/ {{ $coupon->max_uses }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $coupon->id }})" class="text-slate-400 hover:text-blue-600 transition">
                                    <x-fas-edit class="w-4 h-4" />
                                </button>
                                <button onclick="confirm('Supprimer ce code promo ?') || event.stopImmediatePropagation()" wire:click="delete({{ $coupon->id }})" class="text-slate-400 hover:text-red-500 transition">
                                    <x-fas-trash-alt class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500 dark:text-gray-400 italic">
                            Aucun code promo créé pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
