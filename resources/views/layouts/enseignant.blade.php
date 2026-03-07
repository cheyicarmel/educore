<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
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

        @yield('styles')
    </style>
</head>
<body class="bg-background-light text-slate-900 min-h-screen">

<div id="overlay"></div>

{{-- SIDEBAR --}}
<aside id="sidebar">
    <div class="flex items-center gap-3 p-5 shrink-0">
        <div class="bg-primary p-2 rounded-xl text-white shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">school</span>
        </div>
        <span class="logo-text text-xl font-bold text-navy-900">EduCore</span>
    </div>

    <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">

        <a href="{{ route('enseignant.dashboard') }}"
            class="nav-link {{ request()->routeIs('enseignant.dashboard') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">dashboard</span>
            <span class="lbl">Tableau de bord</span>
            <span class="tip">Tableau de bord</span>
        </a>

        <a href="{{ route('enseignant.classes.index') }}"
            class="nav-link {{ request()->routeIs('enseignant.classes.*') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">groups</span>
            <span class="lbl">Mes Classes</span>
            <span class="tip">Mes Classes</span>
        </a>

        {{-- Page Ma Classe visible uniquement si prof principal --}}
        @if(auth()->user()->enseignant?->attributions()
            ->where('annee_academique_id', $anneeActiveLayout?->id)
            ->where('est_prof_principal', true)
            ->exists())
        <a href="{{ route('enseignant.ma-classe') }}"
            class="nav-link {{ request()->routeIs('enseignant.ma-classe') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">class</span>
            <span class="lbl">Ma Classe Principale</span>
            <span class="tip">Ma Classe Principale</span>
        </a>
        @endif

        <a href="{{ route('enseignant.notes.index') }}"
            class="nav-link {{ request()->routeIs('enseignant.notes.*') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">edit_note</span>
            <span class="lbl">Saisie des Notes</span>
            <span class="tip">Saisie des Notes</span>
        </a>

        <div class="pt-2 border-t border-slate-100">
            <a href="{{ route('enseignant.profil') }}"
                class="nav-link {{ request()->routeIs('enseignant.profil') ? 'active' : 'text-navy-700 hover:bg-slate-50' }} flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="lbl">Mon Profil</span>
                <span class="tip">Mon Profil</span>
            </a>
        </div>

    </nav>

    <div class="p-4 border-t border-slate-100 shrink-0">
        <div class="flex items-center gap-3 p-2">
            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">
                {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}{{ strtoupper(substr(auth()->user()->nom, 0, 1)) }}
            </div>
            <div class="user-info min-w-0">
                <p class="text-sm font-semibold truncate">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</p>
                <p class="text-xs text-navy-700 truncate">Enseignant</p>
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

{{-- MAIN --}}
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
                <input class="pl-9 pr-4 py-2 bg-slate-50 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary/20 focus:outline-none"
                    placeholder="Rechercher un élève..." type="text"/>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button class="hidden md:flex p-2 text-slate-500 hover:bg-slate-50 rounded-full relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <div class="hidden lg:flex flex-col text-right ml-2">
                @if($anneeActiveLayout)
                <p class="text-xs font-semibold text-navy-900">Année {{ $anneeActiveLayout->libelle }}</p>
                <p class="text-[10px] text-navy-700 uppercase tracking-wide">Active</p>
                @else
                <p class="text-xs font-semibold text-amber-600">Aucune année active</p>
                @endif
            </div>
            <div class="md:hidden flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">school</span>
            </div>
        </div>
    </header>

    <div class="p-4 md:p-6 lg:p-8 min-h-[calc(100vh-3.5rem-57px)]">
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
    hamburger.addEventListener('click', () => {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    function handleResize() {
        const w = window.innerWidth;
        if (w >= 768 && w < 1024) {
            sidebar.classList.add('collapsed');
            main.classList.add('collapsed');
        } else if (w >= 1024) {
            sidebar.classList.remove('collapsed');
            main.classList.remove('collapsed');
        }
        if (w >= 768) {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        }
    }
    window.addEventListener('resize', handleResize);
    handleResize();

    // ── BARRE DE RECHERCHE CONTEXTUELLE ───────────────────────────
    const searchInput = document.querySelector('input[placeholder="Rechercher un élève..."]');
    const searchWrapper = searchInput?.parentElement;

    // Créer le dropdown
    const dropdown = document.createElement('div');
    dropdown.id = 'search-dropdown';
    dropdown.className = 'absolute top-full left-0 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-lg z-50 hidden overflow-hidden';
    searchWrapper?.appendChild(dropdown);
    if (searchWrapper) searchWrapper.style.position = 'relative';

    // Détecter la page active
    function getPageContext() {
        const path = window.location.pathname;
        if (path.includes('/enseignant/notes'))    return 'notes';
        if (path.includes('/enseignant/ma-classe')) return 'ma-classe';
        if (path.includes('/enseignant/classes'))  return 'classes';
        return null;
    }

    // Collecter les items selon le contexte
    function getItems() {
        const context = getPageContext();
        const items   = [];

        if (context === 'notes' || context === 'ma-classe') {
            document.querySelectorAll('tbody tr[data-inscription]').forEach(row => {
                // Chercher d'abord data-eleve-nom, sinon fallback sur les classes
                const nomEl = row.querySelector('[data-eleve-nom]')
                        ?? row.querySelector('.text-sm.font-bold.text-navy-900')
                        ?? row.querySelector('.text-sm.font-semibold.text-navy-900');
                if (nomEl) {
                    items.push({
                        label: nomEl.textContent.trim(),
                        type:  'eleve',
                        el:    row,
                    });
                }
            });
        }

        if (context === 'classes') {
            document.querySelectorAll('[data-classe-card]').forEach(card => {
                const nomEl = card.querySelector('[data-classe-nom]');
                if (nomEl) {
                    items.push({
                        label: nomEl.textContent.trim(),
                        type:  'classe',
                        el:    card,
                    });
                }
            });
        }

        return items;
    }

    // Highlight temporaire
    function highlightElement(el) {
        el.classList.add('bg-primary/10', 'transition-colors');
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => el.classList.remove('bg-primary/10'), 2500);
    }

    // Afficher le dropdown
    function afficherDropdown(resultats, query) {
        dropdown.innerHTML = '';

        if (resultats.length === 0) {
            dropdown.innerHTML = `
                <div class="px-4 py-3 text-sm text-slate-400 text-center flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-base">search_off</span>
                    Aucun résultat pour "<strong>${query}</strong>"
                </div>`;
            dropdown.classList.remove('hidden');
            return;
        }

        resultats.slice(0, 6).forEach(item => {
            const div = document.createElement('div');
            div.className = 'px-4 py-2.5 flex items-center gap-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-100 last:border-0';

            const icon = item.type === 'eleve' ? 'person' : 'groups';
            const highlighted = item.label.replace(
                new RegExp(query, 'gi'),
                m => `<span class="text-primary font-extrabold">${m}</span>`
            );

            div.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-sm">${icon}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-navy-900 truncate">${highlighted}</p>
                    <p class="text-[11px] text-slate-400">${item.type === 'eleve' ? 'Élève' : 'Classe'}</p>
                </div>
                <span class="material-symbols-outlined text-slate-300 text-sm ml-auto shrink-0">arrow_forward</span>`;

            div.addEventListener('click', () => {
                highlightElement(item.el);
                dropdown.classList.add('hidden');
                searchInput.value = '';
            });

            dropdown.appendChild(div);
        });

        if (resultats.length > 6) {
            const more = document.createElement('div');
            more.className = 'px-4 py-2 text-xs text-slate-400 text-center bg-slate-50';
            more.textContent = `+${resultats.length - 6} autres résultats — affinez votre recherche`;
            dropdown.appendChild(more);
        }

        dropdown.classList.remove('hidden');
    }

    // Écouter la saisie
    searchInput?.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();

        if (!getPageContext()) {
            dropdown.classList.add('hidden');
            return;
        }

        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        const items     = getItems();
        const resultats = items.filter(i => i.label.toLowerCase().includes(query));
        afficherDropdown(resultats, query);
    });

    // Fermer si clic dehors
    document.addEventListener('click', (e) => {
        if (!searchWrapper?.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Placeholder contextuel
    function mettreAJourPlaceholder() {
        const context = getPageContext();
        if (!searchInput) return;
        if (context === 'notes')      searchInput.placeholder = 'Rechercher un élève...';
        else if (context === 'ma-classe') searchInput.placeholder = 'Rechercher un élève...';
        else if (context === 'classes')   searchInput.placeholder = 'Rechercher une classe...';
        else {
            searchInput.placeholder = 'Rechercher...';
            searchInput.disabled    = true;
            searchInput.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
    mettreAJourPlaceholder();
</script>

@yield('scripts')

</body>
</html>