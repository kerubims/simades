<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SIMADES') - Sistem Informasi Manajemen Air Desa</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24; }
        .fill-icon { font-variation-settings: "FILL" 1, "wght" 400, "GRAD" 0, "opsz" 24; }
        .ambient-shadow { box-shadow: 0 4px 12px rgba(0, 103, 103, 0.05); }

        /* Sidebar styles */
        #sidebar { transition: transform 0.25s cubic-bezier(.4,0,.2,1); }
        #sidebar-overlay { transition: opacity 0.25s; }
        .nav-link-active { background: #ffffff; color: #006767; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .nav-link-active .material-symbols-outlined { color: #006767; font-variation-settings: "FILL" 1, "wght" 600, "GRAD" 0, "opsz" 24; }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen antialiased">

@if(session('user_role') === 'admin')
    {{-- ========== ADMIN LAYOUT: SIDEBAR ========== --}}

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="closeMobileSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-[#004f4f] flex flex-col z-50 shadow-2xl
        -translate-x-full md:translate-x-0">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <span class="material-symbols-outlined text-white fill-icon text-3xl flex-shrink-0">water_drop</span>
            <span class="font-headline-md font-bold text-white text-lg">SIMADES</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',       'match' => 'admin.dashboard',           'icon' => 'dashboard',    'label' => 'Dashboard'],
                    ['route' => 'admin.pelanggan.index', 'match' => ['admin.pelanggan.*', 'admin.admins.*'], 'icon' => 'group',        'label' => 'Manajemen User'],
                    ['route' => 'admin.meteran.index',   'match' => 'admin.meteran.*',           'icon' => 'speed',        'label' => 'Catat Meteran'],
                    ['route' => 'admin.tagihan.index',   'match' => 'admin.tagihan.*',           'icon' => 'receipt_long', 'label' => 'Tagihan'],
                    ['route' => 'admin.tarif.index',     'match' => 'admin.tarif.*',       'icon' => 'price_change', 'label' => 'Tarif'],
                    ['route' => 'admin.qris.index',      'match' => 'admin.qris.*',        'icon' => 'qr_code_2',   'label' => 'QRIS Pembayaran'],
                ];
            @endphp

            @foreach($navItems as $item)
                @php $isActive = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}" onclick="closeMobileSidebar()"
                   class="flex items-center gap-3 mx-3 mb-1 px-3 py-2.5 rounded-xl transition-all duration-150
                       {{ $isActive ? 'nav-link-active' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <span class="material-symbols-outlined text-[22px] flex-shrink-0
                        {{ $isActive ? 'fill-icon' : '' }}">{{ $item['icon'] }}</span>
                    <span class="text-sm font-semibold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- User info & logout --}}
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-[16px]">person</span>
                </div>
                <div class="overflow-hidden">
                    <p class="text-white font-semibold text-sm truncate">{{ session('username') }}</p>
                    <p class="text-white/50 text-xs">Administrator</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-red-300 hover:bg-red-500/20 transition-colors">
                    <span class="material-symbols-outlined text-[20px] flex-shrink-0">logout</span>
                    <span class="text-sm font-semibold">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="min-h-screen flex flex-col md:ml-64 pb-16 md:pb-0">

        {{-- Top bar --}}
        <header class="bg-surface border-b border-outline-variant/20 sticky top-0 z-30 px-4 md:px-6 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                {{-- Hamburger (mobile only) --}}
                <button onclick="openMobileSidebar()" class="md:hidden p-1.5 rounded-lg hover:bg-surface-container transition-colors" aria-label="Buka menu">
                    <span class="material-symbols-outlined text-on-surface-variant">menu</span>
                </button>
                <h2 class="font-semibold text-on-surface text-sm">@yield('title', 'SIMADES')</h2>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </header>

        {{-- Alerts --}}
        <div class="px-4 md:px-6 pt-4">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-[#f0fdf4] border border-[#bbf7d0] text-[#166534] flex items-start gap-2">
                    <span class="material-symbols-outlined fill-icon text-[#166534] flex-shrink-0">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-[#fef2f2] border border-[#fecaca] text-[#991b1b] flex items-start gap-2">
                    <span class="material-symbols-outlined fill-icon text-[#991b1b] flex-shrink-0">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-[#fef2f2] border border-[#fecaca] text-[#991b1b]">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 px-4 md:px-6 py-4">
            @yield('content')
        </main>

        <footer class="px-4 md:px-6 py-4 border-t border-outline-variant/20 text-xs text-on-surface-variant text-center hidden md:block">
            &copy; {{ date('Y') }} SIMADES &mdash; Sistem Informasi Manajemen Air Desa
        </footer>
    </div>

    {{-- Mobile bottom navigation --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-surface border-t border-outline-variant/20 flex shadow-lg">
        @php
            $mobileNavItems = [
                ['route' => 'admin.dashboard',       'match' => 'admin.dashboard',   'icon' => 'dashboard',    'label' => 'Dashboard'],
                ['route' => 'admin.pelanggan.index', 'match' => ['admin.pelanggan.*', 'admin.admins.*'], 'icon' => 'group',        'label' => 'User'],
                ['route' => 'admin.meteran.index',   'match' => 'admin.meteran.*',   'icon' => 'speed',        'label' => 'Meteran'],
                ['route' => 'admin.tagihan.index',   'match' => 'admin.tagihan.*',   'icon' => 'receipt_long', 'label' => 'Tagihan'],
                ['route' => 'admin.qris.index',      'match' => 'admin.qris.*',      'icon' => 'qr_code_2',   'label' => 'QRIS'],
            ];
        @endphp
        @foreach($mobileNavItems as $item)
            @php $isActive = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex-1 flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-semibold transition-colors
                   {{ $isActive ? 'text-primary' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px] {{ $isActive ? 'fill-icon' : '' }}">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <script>
        function openMobileSidebar() {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeMobileSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }
    
    </script>

@else
    {{-- ========== WARGA LAYOUT: TOP NAV ========== --}}
    <header class="bg-surface shadow-sm sticky top-0 z-50">
        <div class="flex justify-between items-center w-full px-4 py-3 max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary fill-icon text-3xl">water_drop</span>
                <span class="font-headline-md font-bold text-primary">SIMADES</span>
            </div>
            <nav class="hidden md:flex gap-6 font-label-lg">
                <a href="{{ route('warga.dashboard') }}" class="text-on-surface-variant hover:text-primary {{ request()->routeIs('warga.dashboard') ? 'text-primary font-bold' : '' }}">Dashboard</a>
                <a href="{{ route('warga.riwayat') }}" class="text-on-surface-variant hover:text-primary {{ request()->routeIs('warga.riwayat') ? 'text-primary font-bold' : '' }}">Riwayat Pemakaian</a>
            </nav>
            <div class="flex items-center gap-4">
                @if(session('user_id'))
                    <span class="text-sm font-semibold text-on-surface-variant">Halo, {{ session('username') }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="font-label-lg text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg transition-colors">
                            Logout
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-grow flex flex-col w-full px-4 py-6 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-[#f0fdf4] border border-[#bbf7d0] text-[#166534]">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-[#fef2f2] border border-[#fecaca] text-[#991b1b]">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-[#fef2f2] border border-[#fecaca] text-[#991b1b]">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-surface-container-low mt-auto pb-16 md:pb-0">
        <div class="w-full px-4 py-lg flex flex-col md:flex-row justify-between items-center gap-md max-w-7xl mx-auto">
            <div class="flex items-center gap-sm">
                <span class="material-symbols-outlined text-primary fill-icon text-2xl">home</span>
                <span class="font-headline-sm font-bold text-primary">Dusun Tambak</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface text-center md:text-left">
                &copy; {{ date('Y') }} Dusun Tambak RT/RW 01/09. Desa Lemahbang.
            </p>
            <div class="flex flex-wrap justify-center gap-md font-body-md text-body-md">
                <a class="text-on-surface-variant hover:underline decoration-primary focus:ring-2 focus:ring-primary rounded flex items-center gap-2"
                   href="mailto:kantor.desa@lemahbang.id">
                    <span class="material-symbols-outlined text-sm">mail</span>
                    kantor.desa@lemahbang.id
                </a>
            </div>
        </div>
    </footer>

    {{-- Mobile bottom navigation for Warga --}}
    @if(session('user_id'))
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-30 bg-surface border-t border-outline-variant/20 flex shadow-lg">
        @php
            $wargaNavItems = [
                ['route' => 'warga.dashboard', 'match' => 'warga.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                ['route' => 'warga.riwayat',   'match' => 'warga.riwayat',   'icon' => 'receipt_long', 'label' => 'Tagihan'],
            ];
        @endphp
        @foreach($wargaNavItems as $item)
            @php $isActive = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               class="flex-1 flex flex-col items-center justify-center py-2 gap-0.5 text-[10px] font-semibold transition-colors
                   {{ $isActive ? 'text-primary' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined text-[22px] {{ $isActive ? 'fill-icon' : '' }}">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    @endif
@endif

    <script src="{{ asset('js/pagination.js') }}?v={{ time() }}"></script>
    @yield('scripts')
</body>
</html>
