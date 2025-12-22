<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Estimat' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-blue-900 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('estimations.index') }}" class="text-xl font-black tracking-tighter">ESTIMAT</a>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('estimations.index') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('estimations.*') ? 'bg-blue-800' : '' }}">
                            <x-fas-file-invoice class="w-4 h-4 mr-2" />
                            Estimations
                        </a>
                        <a href="{{ route('settings.project-types') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('settings.project-types') ? 'bg-blue-800' : '' }}">
                            <x-fas-microchip class="w-4 h-4 mr-2" />
                            Types Projet
                        </a>
                        <a href="{{ route('settings.setups') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('settings.setups') ? 'bg-blue-800' : '' }}">
                            <x-fas-cog class="w-4 h-4 mr-2" />
                            Bases Techniques
                        </a>
                        <a href="{{ route('blocks.index') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('blocks.*') ? 'bg-blue-800' : '' }}">
                            <x-fas-cubes class="w-4 h-4 mr-2" />
                            Catalogue Blocs
                        </a>
                        <a href="{{ route('settings.options') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('settings.options') ? 'bg-blue-800' : '' }}">
                            <x-fas-plus-circle class="w-4 h-4 mr-2" />
                            Add-ons
                        </a>
                        <a href="{{ route('settings.translation') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('settings.translation') ? 'bg-blue-800' : '' }}">
                            <x-fas-language class="w-4 h-4 mr-2" />
                            Traduction
                        </a>
                        <a href="{{ route('subscription.index') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('subscription.index') ? 'bg-blue-800' : '' }}">
                            <x-fas-credit-card class="w-4 h-4 mr-2" />
                            Mon Offre
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-2">
                    @if(auth()->user()->is_admin)
                        <div class="h-8 w-px bg-blue-800 mx-2"></div>
                        <a href="{{ route('admin.users') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-800' : '' }}" title="Gérer les Comptes">
                            <x-fas-users class="w-4 h-4" />
                        </a>
                        <a href="{{ route('admin.plans') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.plans') ? 'bg-blue-800' : '' }}" title="Gérer les Plans">
                            <x-fas-crown class="w-4 h-4" />
                        </a>
                        <a href="{{ route('admin.coupons') }}" class="flex items-center hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.coupons') ? 'bg-blue-800' : '' }}" title="Gérer les Coupons">
                            <x-fas-ticket-alt class="w-4 h-4" />
                        </a>
                    @endif
                    <div class="h-8 w-px bg-blue-800 mx-2"></div>
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

    <main class="container mx-auto py-8 px-4">
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
