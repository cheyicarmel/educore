<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'EduCore')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2b6cee",
                        "background-light": "#f6f6f8",
                        "background-dark": "#101622",
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        #sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 17rem;
            z-index: 50;
            display: flex;
            flex-direction: column;
            background: white;
            border-right: 1px solid #e2e8f0;
            transition: transform 0.25s ease, width 0.25s ease;
        }
        #sidebar.collapsed { width: 4.5rem; overflow: visible; }
        #sidebar.collapsed .lbl,
        #sidebar.collapsed .logo-text,
        #sidebar.collapsed .section-label,
        #sidebar.collapsed .user-info,
        #sidebar.collapsed .logout-text { display: none; }
        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding-left: 0; padding-right: 0;
            border-right: none !important;
        }
        #sidebar.collapsed .logout-btn {
            justify-content: center;
            padding-left: 0; padding-right: 0;
        }
        #sidebar.collapsed nav { overflow: visible; }

        .tip {
            display: none;
            position: absolute;
            left: calc(100% + 10px);
            top: 50%; transform: translateY(-50%);
            background: #0d121b; color: white;
            font-size: 0.7rem; font-weight: 600;
            padding: 4px 10px; border-radius: 6px;
            white-space: nowrap; z-index: 999;
            pointer-events: none;
            opacity: 0; transition: opacity 0.15s;
        }
        .tip::before {
            content: '';
            position: absolute; right: 100%; top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #0d121b;
        }
        #sidebar.collapsed .nav-link { position: relative; }
        #sidebar.collapsed .nav-link .tip { display: block; }
        #sidebar.collapsed .nav-link:hover .tip { opacity: 1; }

        @media (max-width: 767px) {
            #sidebar { transform: translateX(-100%); width: 17rem !important; }
            #sidebar.open { transform: translateX(0); }
        }

        .nav-link.active {
            background: rgba(43,108,238,0.1);
            color: #2b6cee;
            border-right: 3px solid #2b6cee;
        }

        #overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 40;
        }
        #overlay.show { display: block; }

        #main {
            margin-left: 17rem;
            min-width: 0;
            transition: margin-left 0.25s ease;
        }
        #main.collapsed { margin-left: 4.5rem; }
        @media (max-width: 767px) {
            #main { margin-left: 0 !important; }
        }

        @yield('styles')
    </style>
</head>


<body class="bg-background-light text-slate-900 min-h-screen">

<div id="overlay"></div>

<!-- SIDEBAR -->
<aside id="sidebar">
    <div class="flex items-center gap-3 p-5 shrink-0">
        <div class="bg-primary p-2 rounded-xl text-white shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">school</span>
        </div>
        <span class="logo-text text-xl font-bold text-navy-900">EduCore</span>
    </div>

    <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">dashboard</span>
            <span class="lbl">Tableau de bord</span>
            <span class="tip">Tableau de bord</span>
        </a>
        <a href="{{ route('admin.annees.index') }}"
           class="nav-link {{ request()->routeIs('admin.annees.index') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">calendar_today</span>
            <span class="lbl">Années Académiques</span>
            <span class="tip">Années Académiques</span>
        </a>
        <a href="{{ route('admin.classes.index') }}"
           class="nav-link {{ request()->routeIs('admin.classes.index') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">groups</span>
            <span class="lbl">Classes</span>
            <span class="tip">Classes</span>
        </a>
        <a href="{{ route('admin.matieres.index') }}"
           class="nav-link {{ request()->routeIs('admin.matieres.index') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">menu_book</span>
            <span class="lbl">Matières</span>
            <span class="tip">Matières</span>
        </a>
        <a href="{{ route('admin.series.index') }}"
           class="nav-link {{ request()->routeIs('admin.series.index') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">category</span>
            <span class="lbl">Séries</span>
            <span class="tip">Séries</span>
        </a>
        <a href="#"
           class="nav-link text-navy-700 hover:bg-slate-50 flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">person_pin_circle</span>
            <span class="lbl">Enseignants</span>
            <span class="tip">Enseignants</span>
        </a>
        <a href="#"
           class="nav-link text-navy-700 hover:bg-slate-50 flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">school</span>
            <span class="lbl">Élèves</span>
            <span class="tip">Élèves</span>
        </a>
        <a href="#"
           class="nav-link text-navy-700 hover:bg-slate-50 flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">payments</span>
            <span class="lbl">Finances</span>
            <span class="tip">Finances</span>
        </a>

        @if(Auth::user()->role === 'superadmin')
        <div class="pt-3 mt-2 border-t border-slate-100">
            <p class="section-label px-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Super Admin</p>
            <a href="#"
               class="nav-link text-navy-700 hover:bg-slate-50 flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">admin_panel_settings</span>
                <span class="lbl">Administrateurs</span>
                <span class="tip">Administrateurs</span>
            </a>
        </div>
        @endif

        <div class="pt-2 border-t border-slate-100">
            <a href="#"
               class="nav-link text-navy-700 hover:bg-slate-50 flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="lbl">Paramètres</span>
                <span class="tip">Paramètres</span>
            </a>
        </div>
    </nav>

    <div class="p-4 border-t border-slate-100 shrink-0">
        <div class="flex items-center gap-3 p-2">
            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                {{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}{{ strtoupper(substr(Auth::user()->nom, 0, 1)) }}
            </div>
            <div class="user-info min-w-0">
                <p class="text-sm font-semibold truncate">{{ Auth::user()->prenom }} {{ Auth::user()->nom }}</p>
                <p class="text-xs text-navy-700 truncate">{{ ucfirst(Auth::user()->role) }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-200 transition-colors">
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
            <div class="relative hidden md:block">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input class="pl-9 pr-4 py-2 bg-slate-50 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary/20 focus:outline-none" placeholder="Rechercher..." type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="hidden md:flex p-2 text-slate-500 hover:bg-slate-50 rounded-full relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <div class="hidden lg:flex flex-col text-right ml-2">
                <p class="text-xs font-semibold text-navy-900">Année 2024-2025</p>
                <p class="text-[10px] text-navy-700 uppercase tracking-wide">Semestre 1 · Active</p>
            </div>
            <div class="md:hidden flex items-center gap-2">
                <div class="p-2 rounded-xl text-white shrink-0">
                    <span class="material-symbols-outlined text-primary w-4 h-4">school</span>
                </div>
            </div>
        </div>
    </header>

    <div class="p-4 md:p-6 lg:p-8 space-y-6 min-h-[calc(100vh-3.5rem-57px)]">
        @yield('content')
    </div>

    <footer class="px-4 py-5 text-center text-slate-400 text-xs border-t border-slate-100">
        © 2026 EduCore — Système de Gestion Scolaire. Tous droits réservés.
    </footer>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const overlay = document.getElementById('overlay');
    const hamburger = document.getElementById('hamburger');
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