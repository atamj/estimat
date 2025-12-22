<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Mon Abonnement</h2>
        <p class="text-gray-600">Gérez votre offre et suivez votre consommation.</p>
    </div>

    @if($plan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="p-6 bg-blue-50 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Offre actuelle</span>
                    <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                </div>
                <div class="text-right">
                    @if($subscription->type === 'lifetime')
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full uppercase">À vie</span>
                    @else
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase">Actif</span>
                    @endif
                </div>
            </div>
            <div class="p-6 grid md:grid-cols-2 gap-8">
                <!-- Quotas -->
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-fas-chart-pie class="w-4 h-4 text-blue-500" />
                        Utilisation des ressources
                    </h4>

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Estimations</span>
                            <span class="font-bold {{ ($plan->max_estimations != -1 && $usage['estimations'] >= $plan->max_estimations) ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $usage['estimations'] }} / {{ $plan->max_estimations == -1 ? '∞' : $plan->max_estimations }}
                            </span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: {{ $plan->max_estimations == -1 ? 0 : min(100, ($usage['estimations'] / $plan->max_estimations) * 100) }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Blocs Catalogue</span>
                            <span class="font-bold {{ ($plan->max_blocks != -1 && $usage['blocks'] >= $plan->max_blocks) ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $usage['blocks'] }} / {{ $plan->max_blocks == -1 ? '∞' : $plan->max_blocks }}
                            </span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: {{ $plan->max_blocks == -1 ? 0 : min(100, ($usage['blocks'] / $plan->max_blocks) * 100) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-fas-star class="w-4 h-4 text-yellow-500" />
                        Inclus dans votre plan
                    </h4>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-2 text-sm {{ $plan->has_white_label_pdf ? 'text-gray-700' : 'text-gray-400' }}">
                            @if($plan->has_white_label_pdf)
                                <x-fas-check-circle class="w-4 h-4 text-green-500" />
                            @else
                                <x-fas-times-circle class="w-4 h-4" />
                            @endif
                            Export PDF Marque Blanche
                        </li>
                        <li class="flex items-center gap-2 text-sm {{ $plan->has_translation_module ? 'text-gray-700' : 'text-gray-400' }}">
                            @if($plan->has_translation_module)
                                <x-fas-check-circle class="w-4 h-4 text-green-500" />
                            @else
                                <x-fas-times-circle class="w-4 h-4" />
                            @endif
                            Module Traduction avancée
                        </li>
                        <li class="flex items-center gap-2 text-sm text-gray-700">
                            <x-fas-check-circle class="w-4 h-4 text-green-500" /> Support par email
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center mb-8">
            <x-fas-exclamation-triangle class="w-12 h-12 text-yellow-500 mx-auto mb-4" />
            <h3 class="text-lg font-bold text-yellow-800 mb-2">Aucun abonnement actif</h3>
            <p class="text-yellow-700 mb-4">Choisissez un plan pour commencer à créer vos estimations professionnelles.</p>
        </div>
    @endif

    <div class="mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Changer d'offre</h3>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($availablePlans as $p)
                <div class="bg-white rounded-xl border {{ $plan && $plan->id === $p->id ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200' }} p-6 flex flex-col">
                    <div class="mb-4">
                        <h4 class="font-bold text-gray-900">{{ $p->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $p->description }}</p>
                    </div>
                    <div class="mb-6">
                        @if($p->price_lifetime)
                            <span class="text-2xl font-bold">{{ $p->price_lifetime }}€</span>
                            <span class="text-xs text-gray-500">une fois</span>
                        @elseif($p->price_monthly == 0)
                            <span class="text-2xl font-bold">Gratuit</span>
                        @else
                            <span class="text-2xl font-bold">{{ $p->price_monthly }}€</span>
                            <span class="text-xs text-gray-500">/mois</span>
                        @endif
                    </div>

                    @if($plan && $plan->id === $p->id)
                        <button disabled class="w-full py-2 px-4 rounded-lg bg-gray-100 text-gray-500 font-bold text-sm cursor-not-allowed">
                            Plan actuel
                        </button>
                    @else
                        <button class="w-full py-2 px-4 rounded-lg bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 transition">
                            Choisir ce plan
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
