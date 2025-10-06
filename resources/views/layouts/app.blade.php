<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Stock Manager' }}</title>

    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <!-- TailwindCSS via CDN for guaranteed styling -->
    <script src="https://cdn.tailwindcss.com"></script>

    
    <!-- Alpine.js pour les interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#EFF6FF',
                            100: '#DBEAFE',
                            200: '#BFDBFE',
                            300: '#93C5FD',
                            400: '#60A5FA',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                            800: '#1E40AF',
                            900: '#1E3A8A',
                        },
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&family=Nunito:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- jQuery (requis pour Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <!-- Searchable Select Component -->
    <script src="{{ asset('js/searchable-select.js') }}"></script>
    
    <style>
        :root {
            --sidebar-expanded: 16rem;
            --sidebar-collapsed: 4.5rem;
        }

        .font-heading {
            font-family: 'Inter', sans-serif;
        }

        .font-body {
            font-family: 'Nunito', sans-serif;
        }

        /* Collapsed sidebar behavior */
        #sidebar.collapsed {
            width: var(--sidebar-collapsed) !important;
        }

        #sidebar.collapsed #brand-text {
            display: none !important;
        }

        #sidebar.collapsed .nav-label {
            display: none !important;
        }

        #sidebar.collapsed .section-label {
            display: none !important;
        }

        #sidebar.collapsed nav a {
            justify-content: center;
            gap: 0;
        }

        #sidebar nav a {
            transition: padding 200ms, gap 200ms;
        }
        
        /* 🔴 BUG FIX C - Alpine.js x-cloak pour éviter flash de contenu */
        [x-cloak] {
            display: none !important;
        }
        
        /* SOLUTION DEFINITIVE - Menu utilisateur au-dessus de TOUT */
        .user-dropdown {
            z-index: 99999 !important;
            position: relative !important;
        }
        
        .dropdown-menu {
            z-index: 99999 !important;
            position: absolute !important;
        }
        
        /* Styles spécifiques pour les champs de recherche dans les en-têtes */
        header .relative:has(input[type="text"]) {
            z-index: 1 !important;
        }

        /* STYLES DE PAGINATION POUR TAILWIND CDN */
        .pagination, .pagination-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pagination nav, .pagination-links nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .pagination .hidden, .pagination-links .hidden {
            display: none;
        }
        .pagination .flex-1, .pagination-links .flex-1 {
            flex: 1 1 0%;
        }
        .pagination p, .pagination-links p {
            color: #6B7280;
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        .pagination span, .pagination a, .pagination-links span, .pagination-links a {
            position: relative;
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            border-width: 1px;
            border-color: #D1D5DB;
            background-color: #FFFFFF;
            color: #6B7280;
            margin-left: -1px;
            text-decoration: none;
        }
        .pagination a:hover, .pagination-links a:hover {
            color: #3B82F6;
            background-color: #EFF6FF;
        }
        .pagination span[aria-disabled="true"], .pagination-links span[aria-disabled="true"] {
            color: #9CA3AF;
            background-color: #F9FAFB;
            cursor: default;
        }
        .pagination span[aria-current="page"] span, .pagination-links span[aria-current="page"] span {
            z-index: 10;
            color: #FFFFFF;
            background-color: #2563EB;
            border-color: #2563EB;
        }
        .pagination a:first-child, .pagination span:first-child, 
        .pagination-links a:first-child, .pagination-links span:first-child {
            border-top-left-radius: 0.375rem;
            border-bottom-left-radius: 0.375rem;
        }
        .pagination a:last-child, .pagination span:last-child,
        .pagination-links a:last-child, .pagination-links span:last-child {
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
        }
        .pagination svg, .pagination-links svg {
            height: 1.25rem;
            width: 1.25rem;
        }
    </style>
</head>

<body class="font-body bg-white text-gray-700">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 w-64 transition-all duration-300 bg-gradient-to-b from-primary-700 to-primary-800 text-white shadow-xl"
            style="width: var(--sidebar-expanded);">
            <!-- Logo + Collapse -->
            <div class="h-16 px-4 flex items-center justify-between border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center shadow">
                        <img src="{{ asset('images/logo.png') }}" alt="logo" class="h-[40px] items-center" />
                    </div>
                    <span id="brand-text" class="font-heading font-semibold tracking-wide">Stock Manager</span>
                </div>
            </div>
            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <ul class="space-y-1">
                    <!-- Dashboard - Accessible à tous les utilisateurs connectés -->
                    @auth
                    @can('view-dashboard')
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5" />
                            </svg>
                            <span class="truncate nav-label">Dashboard</span>
                        </a>
                    </li>
                    @endcan

                    <!-- Section Inventaire -->
                    @if(auth()->user()->can('view-products') || auth()->user()->can('view-stock') || auth()->user()->can('view-movements') || auth()->user()->can('manage-categories'))
                    <li>
                        <div class="text-xs uppercase tracking-wider text-white/70 px-3 pt-4 pb-1 section-label">
                            Inventaire</div>
                        
                        @can('manage-categories')
                        @if(!auth()->user()->isSalesManager())
                        <a href="{{ route('categories.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('categories.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2h-5M4 8v10a2 2 0 002 2h12M4 8l3-3m0 0h6M7 5v3" />
                            </svg>
                            <span class="truncate nav-label">Catégories</span>
                        </a>
                        @endif
                        @endcan

                        @can('view-products')
                        @if(!auth()->user()->isSalesManager())
                        <a href="{{ route('products.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('products.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2h-5M4 8v10a2 2 0 002 2h12M4 8l3-3m0 0h6M7 5v3" />
                            </svg>
                            <span class="truncate nav-label">Produits</span>
                        </a>
                        @endif
                        @endcan

                        @can('view-stock')
                        <a href="{{ route('stock.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('stock.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 7h18M5 11h14M7 15h10M9 19h6" />
                            </svg>
                            <span class="truncate nav-label">Stock</span>
                        </a>
                        @endcan

                        @can('view-movements')
                        <a href="{{ route('movements.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('movements.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M3 12h18M7 8l-4 4 4 4M17 16l4-4-4-4" />
                            </svg>
                            <span class="truncate nav-label">Mouvements</span>
                        </a>
                        @endcan
                    </li>
                    @endif

                    <!-- Section Ventes -->
                    @can('view-sales')
                    <li>
                        <div class="text-xs uppercase tracking-wider text-white/70 px-3 pt-4 pb-1 section-label">Ventes
                        </div>
                        
                        @can('create-sales')
                        @if(!auth()->user()->isSalesManager())
                        <a href="{{ route('quotes.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('quotes.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="truncate nav-label">Devis</span>
                        </a>
                        @endif
                        @endcan
                        
                        <a href="{{ route('invoices.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('invoices.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 7h6M9 11h6M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2h-4L9 5H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate nav-label">Factures</span>
                        </a>
                    </li>
                    @endcan

                    <!-- Section Administration -->
                    @if(auth()->user()->can('manage-users') || auth()->user()->can('manage-roles') || auth()->user()->can('manage-permissions'))
                    <li>
                        <div class="text-xs uppercase tracking-wider text-white/70 px-3 pt-4 pb-1 section-label">
                            Administration</div>
                        
                        @can('manage-users')
                        <a href="{{ route('users.index') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('users.*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 14a4 4 0 10-8 0m8 0v1a4 4 0 11-8 0v-1m12 6v-1a6 6 0 00-12 0v1m-2 0h16" />
                            </svg>
                            <span class="truncate nav-label">Utilisateurs</span>
                        </a>
                        @endcan

                        @can('manage-roles')
                        <a href="{{ route('users.roles') }}"
                            class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('users.roles*') ? 'bg-white/10' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate nav-label">Rôles & Permissions</span>
                        </a>
                        @endcan
                    </li>
                    @endif
                    @endauth
                </ul>
            </nav>
            <div class="p-3 border-t border-white/10">
                <button id="collapse-btn-bottom"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded hover:bg-white/10"
                    title="Réduire/Étendre">
                    <svg id="collapse-icon" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6l-6 6 6 6M18 6l-6 6 6 6" />
                    </svg>
                    <span class="text-sm text-white/80 nav-label">Réduire</span>
                </button>
            </div>
        </aside>

        <!-- Main area -->
        <div class="flex-1 lg:pl-64">
            <!-- Top bar -->
            <header
                class="h-16 flex items-center justify-between px-4 border-b bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden p-2 rounded border"
                        onclick="document.getElementById('sidebar').classList.toggle('hidden')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="font-heading text-lg text-gray-900">{{ $page ?? 'Dashboards' }}</h1>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Icône de notification (seulement si l'utilisateur peut recevoir des alertes) -->
                    @auth
                    @can('receive-stock-alerts')
                    <div class="relative">
                        <button id="notificationBtn" class="p-2 rounded-lg hover:bg-gray-100 transition-colors relative" title="Notifications">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <!-- Badge de notification -->
                            <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                    @endcan
                    
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full border">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-sm">Online</span>
                    </div>

                    <!-- Menu utilisateur -->
                    <!-- 🔴 BUG FIX C : Empêcher ouverture automatique du menu profil -->
                    <div class="relative user-dropdown" x-data="{ open: false }" @click.outside="open = false" style="z-index: 99999 !important; position: relative !important;">
                        <button @click.stop="open = !open" type="button" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center font-heading text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ auth()->user()->getFormattedRoleName() }}</div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Menu déroulant -->
                        <div x-show="open" 
                             x-cloak
                             @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="dropdown-menu absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border py-2" 
                             style="z-index: 99999 !important; position: absolute !important; display: none;">
                            <div class="px-4 py-3 border-b">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-primary-600 text-white flex items-center justify-center font-heading">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                        <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                                        <div class="text-xs text-primary-600 font-medium">{{ auth()->user()->getFormattedRoleName() }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="py-2">
                                @can('manage-users')
                                <a href="{{ route('users.show', auth()->user()) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Mon profil
                                </a>
                                @endcan
                                
                                {{-- Paramètres temporairement masqué - fonctionnalité non implémentée
                                <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Paramètres
                                </a>
                                --}}
                                
                                <div class="border-t my-2"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 w-full text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Se déconnecter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Modal de notifications -->
            <div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
                <div class="flex items-start justify-center min-h-screen pt-16 px-4">
                    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
                        <!-- En-tête du modal -->
                        <div class="flex items-center justify-between p-6 border-b">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-red-100 rounded-lg">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Alertes de Stock</h3>
                                    <p class="text-sm text-gray-500">Produits nécessitant votre attention</p>
                                </div>
                            </div>
                            <button id="closeNotificationModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Contenu du modal -->
                        <div class="p-6 overflow-y-auto max-h-96">
                            <div id="notificationContent">
                                <!-- Le contenu sera chargé dynamiquement -->
                                <div class="flex items-center justify-center py-8">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pied du modal -->
                        <div class="px-6 py-4 bg-gray-50 border-t">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    Dernière mise à jour : <span id="lastUpdateTime">-</span>
                                </p>
                                <button id="refreshNotifications" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                    Actualiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const brandText = document.getElementById('brand-text');
        const collapseBtnBottom = document.getElementById('collapse-btn-bottom');
        const collapseIcon = document.getElementById('collapse-icon');

        function toggleSidebar() {
            const isCollapsed = sidebar.classList.toggle('collapsed');
            if (isCollapsed) {
                collapseIcon.style.transform = 'rotate(180deg)';
            } else {
                collapseIcon.style.transform = 'rotate(0deg)';
            }
        }

        collapseBtnBottom?.addEventListener('click', toggleSidebar);

        // Configuration Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // Affichage des messages de session
        @if (session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if (session('error'))
            toastr.error('{{ session('error') }}');
        @endif

        @if (session('warning'))
            toastr.warning('{{ session('warning') }}');
        @endif

    @if(session('info'))
      toastr.info('{{ session('info') }}');
    @endif

    // Système de notifications
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationModal = document.getElementById('notificationModal');
        const closeNotificationModal = document.getElementById('closeNotificationModal');
        const refreshNotifications = document.getElementById('refreshNotifications');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationContent = document.getElementById('notificationContent');
        const lastUpdateTime = document.getElementById('lastUpdateTime');

        // Charger le nombre de notifications au démarrage
        loadNotificationCount();

        // Actualiser le nombre de notifications toutes les 30 secondes
        setInterval(loadNotificationCount, 30000);

        // Événements
        notificationBtn.addEventListener('click', openNotificationModal);
        closeNotificationModal.addEventListener('click', closeModal);
        refreshNotifications.addEventListener('click', loadNotifications);
        
        // Fermer le modal en cliquant à l'extérieur
        notificationModal.addEventListener('click', function(e) {
            if (e.target === notificationModal) {
                closeModal();
            }
        });

        function loadNotificationCount() {
            fetch('{{ route('notifications.count') }}')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.count);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du nombre de notifications:', error);
                });
        }

        function updateNotificationBadge(count) {
            if (count > 0) {
                notificationBadge.textContent = count > 99 ? '99+' : count;
                notificationBadge.classList.remove('hidden');
                notificationBtn.classList.add('animate-pulse');
            } else {
                notificationBadge.classList.add('hidden');
                notificationBtn.classList.remove('animate-pulse');
            }
        }

        function openNotificationModal() {
            notificationModal.classList.remove('hidden');
            loadNotifications();
        }

        function closeModal() {
            notificationModal.classList.add('hidden');
        }

        function loadNotifications() {
            // Afficher le spinner
            notificationContent.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                </div>
            `;

            fetch('{{ route('notifications.stock') }}')
                .then(response => response.json())
                .then(data => {
                    displayNotifications(data.notifications);
                    lastUpdateTime.textContent = new Date().toLocaleString('fr-FR');
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des notifications:', error);
                    notificationContent.innerHTML = `
                        <div class="text-center py-8">
                            <div class="text-red-500 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500">Erreur lors du chargement des notifications</p>
                        </div>
                    `;
                });
        }

        function displayNotifications(notifications) {
            if (notifications.length === 0) {
                notificationContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-green-500 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tout va bien !</h3>
                        <p class="text-gray-500">Aucune alerte de stock pour le moment</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="space-y-4">';
            
            notifications.forEach(notification => {
                // Gestion des couleurs selon la priorité
                let priorityColor, iconColor, titleColor, icon;
                
                if (notification.priority === 'critical') {
                    // RUPTURE DE STOCK - Rouge foncé avec animation
                    priorityColor = 'border-red-500 bg-red-100 shadow-lg';
                    iconColor = 'text-red-700';
                    titleColor = 'text-red-800';
                    icon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`;
                } else if (notification.priority === 'high') {
                    // STOCK CRITIQUE - Rouge
                    priorityColor = 'border-red-200 bg-red-50';
                    iconColor = 'text-red-600';
                    titleColor = 'text-red-700';
                    icon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`;
                } else {
                    // STOCK FAIBLE - Orange
                    priorityColor = 'border-orange-200 bg-orange-50';
                    iconColor = 'text-orange-600';
                    titleColor = 'text-orange-700';
                    icon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>`;
                }
                
                // Animation pour les ruptures critiques
                const animationClass = notification.priority === 'critical' ? 'animate-pulse' : '';
                
                html += `
                    <div class="border ${priorityColor} rounded-lg p-4 ${animationClass}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    ${icon}
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-medium ${titleColor}">${notification.details.product_name}</h4>
                                    ${notification.priority === 'critical' ? '<span class="text-xs bg-red-600 text-white px-2 py-1 rounded-full font-bold">RUPTURE</span>' : ''}
                                </div>
                                <p class="text-sm text-gray-600 mt-1">SKU: ${notification.details.sku}</p>
                                <p class="text-sm ${titleColor} mt-1 font-medium">${notification.title}</p>
                                
                                <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Stock actuel:</span>
                                        <span class="font-bold ${notification.details.stock_actuel === 0 ? 'text-red-700' : 'text-gray-900'}">${notification.details.stock_actuel}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Seuil critique:</span>
                                        <span class="font-medium text-gray-900">${notification.details.seuil_critique}</span>
                                    </div>
                                    ${notification.priority !== 'critical' ? `
                                    <div>
                                        <span class="text-gray-500">Seuil d'alerte:</span>
                                        <span class="font-medium text-orange-600">${notification.details.seuil_alerte}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Niveau:</span>
                                        <span class="font-medium ${iconColor}">${notification.details.pourcentage}%</span>
                                    </div>
                                    ` : `
                                    <div>
                                        <span class="text-gray-500">Recommandé:</span>
                                        <span class="font-medium text-green-600">${notification.details.quantite_recommandee} unités</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Statut:</span>
                                        <span class="font-bold text-red-700">🚨 URGENT</span>
                                    </div>
                                    `}
                                </div>
                                
                                ${notification.priority === 'critical' ? `
                                <div class="mt-3 p-2 bg-red-200 rounded border-l-4 border-red-500">
                                    <p class="text-xs text-red-800 font-medium">⚠️ Produit en rupture totale - Aucune vente possible</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            notificationContent.innerHTML = html;
        }
    });
  </script>

  <!-- Alertes de stock globales -->
  @include('components.stock-alerts')

  <!-- Scripts personnalisés des pages -->
  @stack('scripts')
</body>

</html>