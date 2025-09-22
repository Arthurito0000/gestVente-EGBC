<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Stock Manager' }}</title>
    <!-- TailwindCSS via CDN for guaranteed styling -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700&family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
      :root { --sidebar-expanded: 16rem; --sidebar-collapsed: 4.5rem; }
      .font-heading { font-family: 'Inter', sans-serif; }
      .font-body { font-family: 'Nunito', sans-serif; }
      /* Collapsed sidebar behavior */
      #sidebar.collapsed { width: var(--sidebar-collapsed) !important; }
      #sidebar.collapsed #brand-text { display: none !important; }
      #sidebar.collapsed .nav-label { display: none !important; }
      #sidebar.collapsed .section-label { display: none !important; }
      #sidebar.collapsed nav a { justify-content: center; gap: 0; }
      #sidebar nav a { transition: padding 200ms, gap 200ms; }
    </style>
</head>
<body class="font-body bg-white text-gray-700">
  <div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 w-64 transition-all duration-300 bg-gradient-to-b from-primary-700 to-primary-800 text-white shadow-xl" style="width: var(--sidebar-expanded);">
      <!-- Logo + Collapse -->
      <div class="h-16 px-4 flex items-center justify-between border-b border-white/10">
        <div class="flex items-center space-x-3">
          <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center shadow">
            <svg class="w-5 h-5 text-primary-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l9 4v6c0 5-3.5 9-9 10C6.5 21 3 17 3 12V6l9-4z"/></svg>
          </div>
          <span id="brand-text" class="font-heading font-semibold tracking-wide">Stock Manager</span>
        </div>
      </div>
      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto px-3 py-4">
        <ul class="space-y-1">
          <li>
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('dashboard') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V21h15V10.5"/></svg>
              <span class="truncate nav-label">Dashboard</span>
            </a>
          </li>
          <li>
            <div class="text-xs uppercase tracking-wider text-white/70 px-3 pt-4 pb-1 section-label">Inventory</div>
            <a href="{{ route('products.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('products.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2h-5M4 8v10a2 2 0 002 2h12M4 8l3-3m0 0h6M7 5v3"/></svg>
              <span class="truncate nav-label">Produits</span>
            </a>
            <a href="{{ route('stock.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('stock.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M5 11h14M7 15h10M9 19h6"/></svg>
              <span class="truncate nav-label">Stock</span>
            </a>
            <a href="{{ route('movements.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('movements.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12h18M7 8l-4 4 4 4M17 16l4-4-4-4"/></svg>
              <span class="truncate nav-label">Mouvements</span>
            </a>
          </li>
          <li>
            <div class="text-xs uppercase tracking-wider text-white/70 px-3 pt-4 pb-1 section-label">Sales</div>
            <a href="{{ route('invoices.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('invoices.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6M9 11h6M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2h-3l-2-2h-4L9 5H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span class="truncate nav-label">Factures</span>
            </a>
          </li>
          <li>
            <div class="text-xs uppercase tracking-wider text-white/70 px-3 pt-4 pb-1 section-label">Administration</div>
            <a href="{{ route('users.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('users.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 14a4 4 0 10-8 0m8 0v1a4 4 0 11-8 0v-1m12 6v-1a6 6 0 00-12 0v1m-2 0h16"/></svg>
              <span class="truncate nav-label">Utilisateurs</span>
            </a>
            <a href="{{ route('roles.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('roles.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <span class="truncate nav-label">Rôles</span>
            </a>
            <a href="{{ route('permissions.index') }}" class="group flex items-center gap-x-3 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('permissions.*') ? 'bg-white/10' : '' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="truncate nav-label">Permissions</span>
            </a>
          </li>
        </ul>
      </nav>
      <div class="p-3 border-t border-white/10">
        <button id="collapse-btn-bottom" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded hover:bg-white/10" title="Réduire/Étendre">
          <svg id="collapse-icon" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6l-6 6 6 6M18 6l-6 6 6 6"/>
          </svg>
          <span class="text-sm text-white/80 nav-label">Réduire</span>
        </button>
      </div>
    </aside>

    <!-- Main area -->
    <div class="flex-1 lg:pl-64">
      <!-- Top bar -->
      <header class="h-16 flex items-center justify-between px-4 border-b bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60">
        <div class="flex items-center gap-3">
          <button class="lg:hidden p-2 rounded border" onclick="document.getElementById('sidebar').classList.toggle('hidden')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
          </button>
          <h1 class="font-heading text-lg text-gray-900">{{ $page ?? 'Dashboard' }}</h1>
        </div>
        <div class="flex items-center gap-3">
          <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full border">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            <span class="text-sm">Online</span>
          </div>
          <div class="w-9 h-9 rounded-full bg-primary-600 text-white flex items-center justify-center font-heading">SM</div>
        </div>
      </header>

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
  </script>
</body>
</html>
