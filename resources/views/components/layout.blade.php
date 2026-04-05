<!DOCTYPE html>
<html lang="fr" x-data="{
    theme: localStorage.getItem('theme') || 'system',
    init() {
        this.$watch('theme', val => {
            localStorage.setItem('theme', val);
            this.apply();
        });
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.theme === 'system') { this.apply(); }
        });
    },
    apply() {
        const dark = this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', dark);
    }
}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Estimat' }}</title>
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'system';
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans">
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="text-xl font-black tracking-tighter">ESTIMAT</a>
                    <div class="hidden md:flex space-x-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">
                            <x-fas-chart-line class="w-4 h-4 mr-2" />
                            Dashboard
                        </a>
                        <a href="{{ route('estimations.index') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('estimations.*') ? 'bg-blue-800' : '' }}">
                            <x-fas-file-invoice class="w-4 h-4 mr-2" />
                            Estimations
                        </a>

                        <a href="{{ route('templates.index') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('templates.*') ? 'bg-blue-800' : '' }}">
                            <x-fas-layer-group class="w-4 h-4 mr-2" />
                            Gabarits
                        </a>

                        @php
                            $inSettings = request()->routeIs('settings.*') || request()->routeIs('blocks.*');
                        @endphp
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-1.5 hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ $inSettings ? 'bg-blue-800' : '' }}">
                                <x-fas-cog class="w-4 h-4" />
                                Paramètres
                                <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute left-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-slate-100 dark:border-gray-700 py-1 z-50" style="display: none;">
                                <a href="{{ route('settings.project-types') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-300 {{ request()->routeIs('settings.project-types') ? 'bg-blue-50 dark:bg-gray-700 text-blue-700 dark:text-blue-300 font-bold' : '' }}">
                                    <x-fas-microchip class="w-4 h-4 text-slate-400 dark:text-gray-500" />
                                    Types de Projet
                                </a>
                                <a href="{{ route('settings.setups') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-300 {{ request()->routeIs('settings.setups') ? 'bg-blue-50 dark:bg-gray-700 text-blue-700 dark:text-blue-300 font-bold' : '' }}">
                                    <x-fas-cog class="w-4 h-4 text-slate-400 dark:text-gray-500" />
                                    Bases Techniques
                                </a>
                                <a href="{{ route('blocks.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-300 {{ request()->routeIs('blocks.*') ? 'bg-blue-50 dark:bg-gray-700 text-blue-700 dark:text-blue-300 font-bold' : '' }}">
                                    <x-fas-cubes class="w-4 h-4 text-slate-400 dark:text-gray-500" />
                                    Catalogue de Blocs
                                </a>
                                <div class="my-1 border-t border-slate-100 dark:border-gray-700"></div>
                                <a href="{{ route('settings.options') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-300 {{ request()->routeIs('settings.options') ? 'bg-blue-50 dark:bg-gray-700 text-blue-700 dark:text-blue-300 font-bold' : '' }}">
                                    <x-fas-plus-circle class="w-4 h-4 text-slate-400 dark:text-gray-500" />
                                    Add-ons
                                </a>
                                <a href="{{ route('settings.translation') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-700 dark:hover:text-blue-300 {{ request()->routeIs('settings.translation') ? 'bg-blue-50 dark:bg-gray-700 text-blue-700 dark:text-blue-300 font-bold' : '' }}">
                                    <x-fas-language class="w-4 h-4 text-slate-400 dark:text-gray-500" />
                                    Traduction
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-2">
                    @if(auth()->user()->is_admin)
                        <div class="h-8 w-px bg-blue-800 mx-2"></div>
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-1.5 hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-800' : '' }}">
                            <x-fas-users class="w-4 h-4" />
                            <span>Comptes</span>
                        </a>
                        <a href="{{ route('admin.plans') }}" class="flex items-center gap-1.5 hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.plans') ? 'bg-blue-800' : '' }}">
                            <x-fas-crown class="w-4 h-4" />
                            <span>Plans</span>
                        </a>
                        <a href="{{ route('admin.coupons') }}" class="flex items-center gap-1.5 hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.coupons') ? 'bg-blue-800' : '' }}">
                            <x-fas-ticket-alt class="w-4 h-4" />
                            <span>Coupons</span>
                        </a>
                    @endif
                    <div class="h-8 w-px bg-blue-800 mx-2"></div>

                    <!-- Bouton thème -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center justify-center w-9 h-9 hover:text-blue-200 hover:bg-blue-800 rounded-md transition" title="Thème">
                            <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>
                            <svg x-show="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <svg x-show="theme === 'system'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-slate-100 dark:border-gray-700 py-1 z-50" style="display: none;">
                            <button @click="theme = 'light'; open = false" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700" :class="{ 'font-bold text-blue-700 dark:text-blue-400': theme === 'light' }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" /></svg>
                                Clair
                            </button>
                            <button @click="theme = 'dark'; open = false" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700" :class="{ 'font-bold text-blue-700 dark:text-blue-400': theme === 'dark' }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                Sombre
                            </button>
                            <button @click="theme = 'system'; open = false" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-slate-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700" :class="{ 'font-bold text-blue-700 dark:text-blue-400': theme === 'system' }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Système
                            </button>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-1.5 hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-blue-800' : '' }}">
                        <x-fas-user-circle class="w-4 h-4" />
                        <span>Mon Compte</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium" title="Déconnexion">
                            <x-fas-sign-out-alt class="w-4 h-4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto py-8 px-4 dark:bg-gray-900">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
