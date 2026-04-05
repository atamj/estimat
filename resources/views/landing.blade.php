<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimat - L'outil d'estimation Saas pour les pros du web</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased" x-data="{ billing: 'monthly' }">

    <!-- Header / Nav -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 p-1.5 rounded-lg">
                        <x-fas-file-invoice class="w-6 h-6 text-white" />
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 uppercase">Estimat</span>
                </div>
                <div class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                    <a href="#features" class="text-slate-600 hover:text-blue-600 transition">Fonctionnalités</a>
                    <a href="#pricing" class="text-slate-600 hover:text-blue-600 transition">Tarifs</a>
                    <a href="#faq" class="text-slate-600 hover:text-blue-600 transition">FAQ</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('estimations.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Mon Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Connexion</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-full hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            Essai Gratuit
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-20 pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold mb-6 animate-bounce">
                    🚀 Nouveau : Gérez vos estimations comme un pro
                </div>
                <h1 class="text-5xl md:text-6xl font-extrabold text-slate-900 tracking-tight mb-6">
                    L'estimation de projets web, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">enfin simplifiée.</span>
                </h1>
                <p class="text-xl text-slate-600 mb-10 leading-relaxed">
                    Créez des devis précis en quelques minutes. Gérez vos blocs, vos technos et vos add-ons dans une interface intuitive pensée pour les développeurs et agences.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white bg-blue-600 rounded-2xl hover:bg-blue-700 transition shadow-xl shadow-blue-200">
                        Commencer gratuitement
                    </a>
                    <a href="#pricing" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 transition">
                        Voir les tarifs
                    </a>
                </div>
                <div class="mt-12 flex items-center justify-center gap-6 text-slate-400">
                    <div class="flex items-center gap-2">
                        <x-fas-check class="w-4 h-4 text-green-500" />
                        <span class="text-sm font-medium">Sans carte bancaire</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-fas-check class="w-4 h-4 text-green-500" />
                        <span class="text-sm font-medium">Installation en 1 clic</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Preview Mockup -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 mb-32 relative">
        <div class="bg-slate-900 rounded-3xl p-2 shadow-2xl shadow-slate-300">
            <div class="bg-slate-800 rounded-2xl overflow-hidden border border-slate-700">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=2426&ixlib=rb-4.0.3" alt="App Preview" class="w-full opacity-80">
            </div>
        </div>
        <div class="absolute -z-10 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-blue-100/50 blur-3xl rounded-full"></div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-blue-600 font-bold uppercase tracking-wider text-sm mb-3">Fonctionnalités</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Tout ce dont vous avez besoin pour chiffrer</h3>
            </div>
            <div class="grid md:grid-cols-3 gap-12">
                <div class="group">
                    <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <x-fas-cubes class="w-7 h-7" />
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Catalogue de Blocs</h4>
                    <p class="text-slate-600 leading-relaxed">Organisez vos fonctionnalités réutilisables. Définissez vos temps de prog, d'inté et de contenu une fois pour toutes.</p>
                </div>
                <div class="group">
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <x-fas-microchip class="w-7 h-7" />
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Multi-Techno</h4>
                    <p class="text-slate-600 leading-relaxed">Créez des configurations spécifiques pour WordPress, Laravel ou du sur-mesure. Filtrez vos blocs selon la techno.</p>
                </div>
                <div class="group">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <x-fas-globe class="w-7 h-7" />
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Module Traduction</h4>
                    <p class="text-slate-600 leading-relaxed">Gérez le multilingue intelligemment. Le système adapte les surcharges selon le nombre de langues choisies.</p>
                </div>
                <div class="group">
                    <div class="w-14 h-14 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <x-fas-file-pdf class="w-7 h-7" />
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Export PDF Pro</h4>
                    <p class="text-slate-600 leading-relaxed">Générez des devis détaillés et professionnels en un clic. Prêt à être envoyé à vos clients.</p>
                </div>
                <div class="group">
                    <div class="w-14 h-14 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <x-fas-layer-group class="w-7 h-7" />
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Gabarits Similaires</h4>
                    <p class="text-slate-600 leading-relaxed">Gagnez du temps sur les contenus répétitifs comme les blogs ou les catalogues avec la multiplication intelligente.</p>
                </div>
                <div class="group">
                    <div class="w-14 h-14 bg-pink-100 text-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <x-fas-cog class="w-7 h-7" />
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900">Add-ons Globaux</h4>
                    <p class="text-slate-600 leading-relaxed">Ajoutez des options comme le SEO ou la maintenance avec des règles de calcul flexibles (fixe, %, heures).</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center mb-12">
                <h2 class="text-blue-600 font-bold uppercase tracking-wider text-sm mb-3">Tarifs</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900 tracking-tight">Le plan idéal pour votre activité</h3>

                <!-- Toggle Monthly/Yearly -->
                <div class="mt-8 flex items-center justify-center gap-4">
                    <span class="text-sm font-semibold" :class="billing === 'monthly' ? 'text-slate-900' : 'text-slate-400'">Mensuel</span>
                    <button @click="billing = billing === 'monthly' ? 'yearly' : 'monthly'" class="relative w-14 h-7 bg-slate-200 rounded-full transition p-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="w-5 h-5 bg-white rounded-full shadow-md transform transition" :class="billing === 'monthly' ? 'translate-x-0' : 'translate-x-7'"></div>
                    </button>
                    <span class="text-sm font-semibold flex items-center gap-2" :class="billing === 'yearly' ? 'text-slate-900' : 'text-slate-400'">
                        Annuel <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full uppercase font-bold">-20%</span>
                    </span>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8 items-start">
                @foreach($plans as $plan)
                    @if($plan->slug === 'free')
                        <!-- Free Plan -->
                        <div class="bg-white rounded-3xl p-8 border border-slate-200 flex flex-col h-full hover:shadow-xl transition duration-300">
                            <h4 class="text-lg font-bold text-slate-900 mb-2">{{ $plan->name }}</h4>
                            <div class="flex items-baseline gap-1 mb-6">
                                <span class="text-4xl font-extrabold text-slate-900">0€</span>
                            </div>
                            <p class="text-slate-500 text-sm mb-8">{{ $plan->description }}</p>
                            <ul class="space-y-4 mb-8 flex-grow">
                                <li class="flex items-center gap-3 text-sm text-slate-600">
                                    <x-fas-check class="w-4 h-4 text-green-500" /> {{ $plan->max_estimations === -1 ? 'Estimations illimitées' : $plan->max_estimations . ' Estimation' . ($plan->max_estimations > 1 ? 's' : '') }}
                                </li>
                                <li class="flex items-center gap-3 text-sm text-slate-600">
                                    <x-fas-check class="w-4 h-4 text-green-500" /> {{ $plan->max_blocks === -1 ? 'Catalogue illimité' : $plan->max_blocks . ' Bloc' . ($plan->max_blocks > 1 ? 's' : '') . ' au catalogue' }}
                                </li>
                                <li class="flex items-center gap-3 text-sm text-slate-400">
                                    @if($plan->has_white_label_pdf)
                                        <x-fas-check class="w-4 h-4 text-green-500" /> Export PDF Marque Blanche
                                    @else
                                        <x-fas-times class="w-4 h-4" /> Export PDF (watermark)
                                    @endif
                                </li>
                            </ul>
                            <a href="{{ route('register', ['plan' => 'free', 'billing_cycle' => 'monthly']) }}" class="w-full py-3 px-6 rounded-xl border-2 border-slate-100 text-center font-bold text-slate-600 hover:bg-slate-50 transition">
                                Essayer
                            </a>
                        </div>
                    @elseif($plan->slug === 'pro')
                        <!-- Pro Plan -->
                        <div class="bg-white rounded-3xl p-8 border-2 border-blue-600 flex flex-col h-full relative shadow-2xl shadow-blue-100 transform md:-translate-y-4">
                            <div class="absolute top-0 right-8 -translate-y-1/2 bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Le plus populaire</div>
                            <h4 class="text-xl font-bold text-slate-900 mb-2">{{ $plan->name }}</h4>
                            <div class="flex items-baseline gap-1 mb-6">
                                <span class="text-5xl font-extrabold text-slate-900" x-text="billing === 'monthly' ? '{{ $plan->price_monthly }}€' : '{{ round($plan->price_yearly / 12) }}€'"></span>
                                <span class="text-slate-500 font-medium" x-text="billing === 'monthly' ? '/mois' : '/mois HT'"></span>
                            </div>
                            <p class="text-slate-500 text-sm mb-8">{{ $plan->description }}</p>
                            <div class="grid md:grid-cols-1 gap-4 mb-10">
                                <ul class="space-y-4 flex-grow">
                                    <li class="flex items-center gap-3 text-sm text-slate-600 font-semibold">
                                        <x-fas-check class="w-4 h-4 text-blue-500" /> Estimations illimitées
                                    </li>
                                    <li class="flex items-center gap-3 text-sm text-slate-600">
                                        <x-fas-check class="w-4 h-4 text-blue-500" /> Catalogue illimité
                                    </li>
                                    <li class="flex items-center gap-3 text-sm text-slate-600">
                                        <x-fas-check class="w-4 h-4 text-blue-500" /> Export PDF Marque Blanche
                                    </li>
                                    @if($plan->has_translation_module)
                                        <li class="flex items-center gap-3 text-sm text-slate-600">
                                            <x-fas-check class="w-4 h-4 text-blue-500" /> Module Traduction
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <a
                                x-bind:href="`{{ url('/register') }}?plan=pro&billing_cycle=${billing === 'monthly' ? 'monthly' : 'yearly'}`"
                                href="{{ route('register', ['plan' => 'pro', 'billing_cycle' => 'monthly']) }}"
                                class="w-full py-4 px-6 rounded-2xl bg-blue-600 text-center font-bold text-white hover:bg-blue-700 transition shadow-lg shadow-blue-200"
                            >
                                Prendre le plan Pro
                            </a>
                        </div>
                    @elseif($plan->slug === 'pioneer')
                        <!-- Pioneer Plan -->
                        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-8 text-white flex flex-col h-full hover:shadow-xl transition duration-300 relative overflow-hidden">
                            <div class="absolute -right-8 -top-8 w-24 h-24 bg-yellow-500/20 rounded-full blur-2xl"></div>
                            <h4 class="text-lg font-bold mb-2 flex items-center gap-2">
                                {{ $plan->name }}
                                <span class="bg-yellow-500 text-slate-900 text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-tighter">Lifetime</span>
                            </h4>
                            <div class="flex items-baseline gap-1 mb-4">
                                <span class="text-4xl font-extrabold text-white">{{ $plan->price_lifetime }}€</span>
                            </div>
                            <div class="bg-white/10 rounded-lg p-3 mb-6">
                                <p class="text-[11px] text-slate-300 uppercase font-bold tracking-widest mb-1">Stock limité</p>
                                <div class="h-1.5 w-full bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full w-[15%] bg-yellow-500"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-1 italic">Offre spéciale pionnier</p>
                            </div>
                            <ul class="space-y-4 mb-8 flex-grow">
                                <li class="flex items-center gap-3 text-sm text-slate-300">
                                    <x-fas-check class="w-4 h-4 text-yellow-500" /> Payez une fois, utilisez à vie
                                </li>
                                <li class="flex items-center gap-3 text-sm text-slate-300">
                                    <x-fas-check class="w-4 h-4 text-yellow-500" /> Toutes les futures mises à jour
                                </li>
                                <li class="flex items-center gap-3 text-sm text-slate-300">
                                    <x-fas-check class="w-4 h-4 text-yellow-500" /> Badge "Pionnier" profil
                                </li>
                            </ul>
                            <a href="{{ route('register', ['plan' => 'pioneer', 'billing_cycle' => 'lifetime']) }}" class="w-full py-3 px-6 rounded-xl bg-white text-center font-bold text-slate-900 hover:bg-slate-100 transition shadow-lg">
                                Devenir Pionnier
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

            <p class="mt-12 text-center text-slate-500 text-sm italic flex items-center justify-center gap-2">
                <x-fas-shield-alt class="w-4 h-4" /> Paiement sécurisé par Stripe. Résiliable à tout moment.
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold text-slate-900">Questions fréquentes</h3>
            </div>
            <div class="space-y-6" x-data="{ active: null }">
                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 transition">
                        <span class="font-bold text-slate-900">Puis-je importer mon catalogue existant ?</span>
                        <x-fas-chevron-down class="w-4 h-4 text-slate-400 transform transition" x-bind:class="active === 1 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="active === 1" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed">
                        Absolument. Estimat permet de créer rapidement vos blocs. Une fonction d'import CSV/Excel sera disponible prochainement dans le cadre du plan Pro.
                    </div>
                </div>
                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 transition">
                        <span class="font-bold text-slate-900">Est-ce que l'export PDF est personnalisable ?</span>
                        <x-fas-chevron-down class="w-4 h-4 text-slate-400 transform transition" x-bind:class="active === 2 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="active === 2" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed">
                        Le plan Pro vous permet d'exporter vos devis en marque blanche, avec vos propres coordonnées. La personnalisation du design (couleurs, logo) fait partie de notre roadmap actuelle.
                    </div>
                </div>
                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 transition">
                        <span class="font-bold text-slate-900">Pourquoi un plan Pionnier ?</span>
                        <x-fas-chevron-down class="w-4 h-4 text-slate-400 transform transition" x-bind:class="active === 3 ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="active === 3" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed">
                        Nous croyons au développement indépendant et à l'écoute de notre communauté. Le plan Pionnier nous aide à financer les serveurs et les nouvelles fonctionnalités tout en remerciant les premiers utilisateurs avec un accès à vie très avantageux.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 py-12 text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="bg-blue-600 p-1.5 rounded-lg">
                        <x-fas-file-invoice class="w-6 h-6 text-white" />
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-white uppercase">Estimat</span>
                </div>
                <div class="flex gap-8 text-sm">
                    <a href="#" class="hover:text-white transition">CGU</a>
                    <a href="#" class="hover:text-white transition">Confidentialité</a>
                    <a href="#" class="hover:text-white transition">Contact</a>
                </div>
                <div class="text-sm">
                    &copy; {{ date('Y') }} Estimat. Tous droits réservés.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
