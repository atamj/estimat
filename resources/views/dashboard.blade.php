<x-layout>
    <x-slot:title>Dashboard</x-slot:title>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black text-blue-900 dark:text-gray-100">Bonjour, {{ auth()->user()->name }} 👋</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ now()->translatedFormat('l j F Y') }}</p>
        </div>
        <a href="{{ route('estimations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2">
            <x-fas-plus class="w-4 h-4" />
            Nouvelle estimation
        </a>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total estimations</span>
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                    <x-fas-file-invoice class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ $totalEstimations }}</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $thisMonthCount }} ce mois-ci</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Montant estimé</span>
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center">
                    <x-fas-euro-sign class="w-4 h-4 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalFixedRevenue, 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">+{{ number_format($thisMonthRevenue, 0, ',', ' ') }} ce mois (forfait)</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Heures estimées</span>
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center">
                    <x-fas-clock class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($totalHours, 1) }}<span class="text-lg font-medium text-gray-400">h</span></div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">+{{ number_format($thisMonthHours, 1) }}h ce mois</div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Mon plan</span>
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/40 rounded-lg flex items-center justify-center">
                    <x-fas-star class="w-4 h-4 text-orange-500 dark:text-orange-400" />
                </div>
            </div>
            <div class="text-xl font-black text-gray-900 dark:text-white">{{ $plan?->name ?? 'Gratuit' }}</div>
            @if($plan && $plan->max_estimations > 0)
                <div class="mt-2">
                    <div class="flex justify-between text-xs text-gray-400 dark:text-gray-500 mb-1">
                        <span>{{ $totalEstimations }} / {{ $plan->max_estimations }} estimations</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="bg-orange-500 h-1.5 rounded-full transition-all" style="width: {{ min(100, ($totalEstimations / $plan->max_estimations) * 100) }}%"></div>
                    </div>
                </div>
            @else
                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Estimations illimitées</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Estimations récentes --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="font-bold text-gray-800 dark:text-gray-100">Estimations récentes</h2>
                <a href="{{ route('estimations.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">Voir tout →</a>
            </div>
            @if($recentEstimations->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-500">
                    <x-fas-file-invoice class="w-10 h-10 mb-3 opacity-30" />
                    <p class="text-sm">Aucune estimation pour le moment</p>
                    <a href="{{ route('estimations.create') }}" class="mt-3 text-xs text-blue-600 dark:text-blue-400 hover:underline">Créer ma première estimation</a>
                </div>
            @else
                <div class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($recentEstimations as $estimation)
                        <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($estimation->projectType?->icon)
                                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shrink-0">
                                        <x-dynamic-component :component="$estimation->projectType->icon" class="w-4 h-4 text-blue-500 dark:text-blue-400" />
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shrink-0">
                                        <x-fas-file class="w-4 h-4 text-gray-400" />
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-semibold text-sm text-gray-800 dark:text-gray-100 truncate">{{ $estimation->project_name ?: $estimation->client_name }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500">{{ $estimation->client_name }} · {{ $estimation->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 shrink-0 ml-4">
                                <div class="text-right">
                                    @if($estimation->type === 'hour')
                                        <div class="text-sm font-bold text-purple-600 dark:text-purple-400">{{ number_format($estimation->_total_time, 1) }}h</div>
                                    @else
                                        <div class="text-sm font-bold text-green-600 dark:text-green-400">{{ number_format($estimation->_total_price, 0, ',', ' ') }} {{ $estimation->currency_symbol }}</div>
                                    @endif
                                </div>
                                <a href="{{ route('estimations.builder', $estimation) }}" class="opacity-0 group-hover:opacity-100 transition-opacity text-blue-500 hover:text-blue-700" title="Ouvrir">
                                    <x-fas-arrow-right class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Colonne droite --}}
        <div class="flex flex-col gap-6">
            {{-- Par type de projet --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">Par type de projet</h2>
                </div>
                @if($byProjectType->isEmpty())
                    <div class="px-6 py-6 text-sm text-gray-400 dark:text-gray-500 text-center">Aucune donnée</div>
                @else
                    <div class="px-6 py-4 space-y-3">
                        @php $maxCount = $byProjectType->max('count'); @endphp
                        @foreach($byProjectType as $typeName => $data)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <div class="flex items-center gap-2 font-medium text-gray-700 dark:text-gray-300">
                                        @if($data['icon'])
                                            <x-dynamic-component :component="$data['icon']" class="w-3.5 h-3.5 text-blue-500" />
                                        @endif
                                        <span class="truncate max-w-32">{{ $typeName }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 shrink-0">{{ $data['count'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-blue-500 h-1.5 rounded-full transition-all" style="width: {{ $maxCount > 0 ? ($data['count'] / $maxCount) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Plan détail --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-800 dark:text-gray-100">Mon abonnement</h2>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Plan actif</span>
                        <span class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $plan?->name ?? 'Gratuit' }}</span>
                    </div>
                    @if($subscription)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Type</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                {{ $subscription->type === 'lifetime' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                {{ match($subscription->type) { 'monthly' => 'Mensuel', 'yearly' => 'Annuel', 'lifetime' => 'À vie', default => $subscription->type } }}
                            </span>
                        </div>
                        @if($subscription->ends_at)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Renouvellement</span>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $subscription->ends_at->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    @endif
                    @if($plan)
                        <div class="pt-2 border-t border-gray-100 dark:border-gray-700 space-y-2">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <x-fas-check class="w-3 h-3 text-green-500 shrink-0" />
                                {{ $plan->max_estimations < 0 ? 'Estimations illimitées' : $plan->max_estimations . ' estimations max' }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <x-fas-check class="w-3 h-3 text-green-500 shrink-0" />
                                {{ $plan->max_blocks < 0 ? 'Blocs illimités' : $plan->max_blocks . ' blocs personnalisés' }}
                            </div>
                            @if($plan->has_translation_module)
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <x-fas-check class="w-3 h-3 text-green-500 shrink-0" />
                                    Module traduction inclus
                                </div>
                            @endif
                            @if($plan->has_white_label_pdf)
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <x-fas-check class="w-3 h-3 text-green-500 shrink-0" />
                                    PDF sans marque
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
