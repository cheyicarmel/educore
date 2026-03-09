<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'EduCore — Comptable')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        "primary": "#2b6cee",
                        "background-light": "#f6f6f8",
                        "navy-900": "#0d121b",
                        "navy-700": "#4c669a",
                    },
                    fontFamily: { "display": ["Lexend", "sans-serif"] },
                },
            },
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Lexend', sans-serif; overflow-x: hidden; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

        #sidebar {
            position: fixed; top: 0; left: 0;
            height: 100%; width: 17rem; z-index: 50;
            display: flex; flex-direction: column;
            background: white; border-right: 1px solid #e2e8f0;
            transition: transform 0.25s ease, width 0.25s ease;
        }
        #sidebar.collapsed { width: 4.5rem; overflow: visible; }
        #sidebar.collapsed .lbl, #sidebar.collapsed .logo-text,
        #sidebar.collapsed .user-info, #sidebar.collapsed .logout-text { display: none; }
        #sidebar.collapsed .nav-link { justify-content: center; padding-left: 0; padding-right: 0; border-right: none !important; }
        #sidebar.collapsed .logout-btn { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed nav { overflow: visible; }

        .tip {
            display: none; position: absolute;
            left: calc(100% + 10px); top: 50%; transform: translateY(-50%);
            background: #0d121b; color: white;
            font-size: 0.7rem; font-weight: 600;
            padding: 4px 10px; border-radius: 6px;
            white-space: nowrap; z-index: 999;
            pointer-events: none; opacity: 0; transition: opacity 0.15s;
        }
        .tip::before {
            content: ''; position: absolute; right: 100%; top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent; border-right-color: #0d121b;
        }
        #sidebar.collapsed .nav-link { position: relative; }
        #sidebar.collapsed .nav-link .tip { display: block; }
        #sidebar.collapsed .nav-link:hover .tip { opacity: 1; }

        @media (max-width: 767px) {
            #sidebar { transform: translateX(-100%); width: 17rem !important; }
            #sidebar.open { transform: translateX(0); }
        }
        .nav-link.active { background: rgba(43,108,238,0.1); color: #2b6cee; border-right: 3px solid #2b6cee; }

        #overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 40; }
        #overlay.show { display: block; }

        #main { margin-left: 17rem; min-width: 0; transition: margin-left 0.25s ease; }
        #main.collapsed { margin-left: 4.5rem; }
        @media (max-width: 767px) { #main { margin-left: 0 !important; } }
    </style>
    @yield('styles')
</head>
<body class="bg-background-light text-slate-900 min-h-screen">

<div id="overlay"></div>

<!-- SIDEBAR -->
<aside id="sidebar">
    {{-- Logo --}}
    <div class="flex items-center gap-3 p-5 shrink-0">
        <div class="bg-primary p-2 rounded-xl text-white shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">school</span>
        </div>
        <span class="logo-text text-xl font-bold text-navy-900">EduCore</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">
        <a href="{{ route('comptable.dashboard') }}"
            class="nav-link {{ request()->routeIs('comptable.dashboard') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">dashboard</span>
            <span class="lbl">Tableau de bord</span>
            <span class="tip">Tableau de bord</span>
        </a>
        <a href="{{ route('comptable.paiements.create') }}"
            class="nav-link {{ request()->routeIs('comptable.paiements.create') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">add_card</span>
            <span class="lbl">Enregistrer Paiement</span>
            <span class="tip">Enregistrer Paiement</span>
        </a>
        <a href="{{ route('comptable.paiements.index') }}"
            class="nav-link {{ request()->routeIs('comptable.paiements.index') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">receipt_long</span>
            <span class="lbl">Historique Paiements</span>
            <span class="tip">Historique Paiements</span>
        </a>
        <a href="{{ route('comptable.suivi') }}"
            class="nav-link {{ request()->routeIs('comptable.suivi') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">account_balance_wallet</span>
            <span class="lbl">Suivi Financier</span>
            <span class="tip">Suivi Financier</span>
        </a>
        <a href="{{ route('comptable.retards') }}"
            class="nav-link {{ request()->routeIs('comptable.retards') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">warning</span>
            <span class="lbl">Retards de Paiement</span>
            <span class="tip">Retards de Paiement</span>
        </a>
        <div class="pt-2 border-t border-slate-100">
            <a href="{{ route('comptable.profil') }}"
                class="nav-link {{ request()->routeIs('comptable.profil') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">person</span>
                <span class="lbl">Mon Profil</span>
                <span class="tip">Mon Profil</span>
            </a>
        </div>
    </nav>

    {{-- User + Logout --}}
    <div class="p-4 border-t border-slate-100 shrink-0">
        <div class="flex items-center gap-3 p-2">
            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs shrink-0">
                {{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}{{ strtoupper(substr(Auth::user()->nom, 0, 1)) }}
            </div>
            <div class="user-info min-w-0">
                <p class="text-sm font-semibold truncate">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
                <p class="text-xs text-navy-700 truncate">Comptable</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-base shrink-0">logout</span>
                <span class="logout-text">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div id="main">
    <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <button id="hamburger" class="md:hidden p-2 text-slate-500 hover:bg-slate-50 rounded-lg">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <button id="toggle-collapse" class="hidden md:flex p-2 text-slate-500 hover:bg-slate-50 rounded-lg">
                <span class="material-symbols-outlined">menu</span>
            </button>
            {{-- Mobile logo --}}
            <div class="md:hidden flex items-center gap-2">
                <div class="bg-primary p-1.5 rounded-lg text-white">
                    <span class="material-symbols-outlined text-base">school</span>
                </div>
                <span class="text-sm font-bold text-navy-900">EduCore</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="hidden lg:flex flex-col text-right ml-2">
                <p class="text-xs font-semibold text-navy-900">{{ $anneeActiveLayout?->libelle ?? '—' }}</p>
                <p class="text-[10px] text-navy-700 uppercase tracking-wide">Année Active</p>
            </div>
        </div>
    </header>

    <div class="p-4 md:p-6 lg:p-8 space-y-6">
        @yield('content')
    </div>

    <footer class="px-4 py-5 text-center text-slate-400 text-xs border-t border-slate-100">
        © 2026 EduCore — Système de Gestion Scolaire. Tous droits réservés.
    </footer>
</div>

<script>
    const sidebar  = document.getElementById('sidebar');
    const main     = document.getElementById('main');
    const overlay  = document.getElementById('overlay');
    const hamburger      = document.getElementById('hamburger');
    const toggleCollapse = document.getElementById('toggle-collapse');

    toggleCollapse.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        main.classList.toggle('collapsed', isCollapsed);
    });
    hamburger.addEventListener('click', () => { sidebar.classList.add('open'); overlay.classList.add('show'); });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });

    function handleResize() {
        const w = window.innerWidth;
        if (w >= 768 && w < 1024) { sidebar.classList.add('collapsed'); main.classList.add('collapsed'); }
        else if (w >= 1024) { sidebar.classList.remove('collapsed'); main.classList.remove('collapsed'); }
        if (w >= 768) { sidebar.classList.remove('open'); overlay.classList.remove('show'); }
    }
    window.addEventListener('resize', handleResize);
    handleResize();
</script>
@yield('scripts')
</body>
</html>