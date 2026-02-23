<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tableau de Bord Élève - EduCore</title>
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
        <a href="#" class="nav-link active flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">dashboard</span>
            <span class="lbl">Tableau de bord</span>
            <span class="tip">Tableau de bord</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">grade</span>
            <span class="lbl">Mes Notes</span>
            <span class="tip">Mes Notes</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">calculate</span>
            <span class="lbl">Mes Moyennes</span>
            <span class="tip">Mes Moyennes</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">description</span>
            <span class="lbl">Mes Bulletins</span>
            <span class="tip">Mes Bulletins</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">payments</span>
            <span class="lbl">Mes Paiements</span>
            <span class="tip">Mes Paiements</span>
        </a>
        <div class="pt-2 border-t border-slate-100">
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">settings</span>
                <span class="lbl">Paramètres</span>
                <span class="tip">Paramètres</span>
            </a>
        </div>
    </nav>

    <div class="p-4 border-t border-slate-100 shrink-0">
        <div class="flex items-center gap-3 p-2">
            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">KM</div>
            <div class="user-info min-w-0">
                <p class="text-sm font-semibold truncate">Kofi Mensah</p>
                <p class="text-xs text-navy-700 truncate">Élève · 4ème C</p>
            </div>
        </div>
        <button class="logout-btn w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-200 transition-colors">
            <span class="material-symbols-outlined text-base shrink-0">logout</span>
            <span class="logout-text">Déconnexion</span>
        </button>
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
        </div>
        <div class="flex items-center gap-2">
            <div class="hidden lg:flex flex-col text-right ml-2">
                <p class="text-xs font-semibold text-navy-900">Année 2024-2025</p>
                <p class="text-[10px] text-navy-700 uppercase tracking-wide">Semestre 1 · Active</p>
            </div>
            <div class="md:hidden flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">school</span>
            </div>
        </div>
    </header>

    <div class="p-4 md:p-6 lg:p-8 space-y-6">

        <!-- Heading + badge classe -->
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mon Espace</h1>
                <p class="text-sm text-navy-700 mt-1">Bienvenue, Kofi — voici un résumé de ta scolarité.</p>
            </div>
            <div class="shrink-0 text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-xs font-bold rounded-full">
                    <span class="material-symbols-outlined text-sm">groups</span>
                    4ème C
                </span>
            </div>
        </div>

        <!-- KPI -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">calculate</span>
                    </div>
                </div>
                <p class="text-navy-700 text-xs font-medium">Moyenne Générale S1</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">12,4<span class="text-sm font-medium text-navy-700">/20</span></h3>
            </div>
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">leaderboard</span>
                    </div>
                </div>
                <p class="text-navy-700 text-xs font-medium">Classement</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">5<span class="text-sm font-medium text-navy-700">ème / 38</span></h3>
            </div>
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">grade</span>
                    </div>
                </div>
                <p class="text-navy-700 text-xs font-medium">Notes Reçues</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">18<span class="text-sm font-medium text-navy-700">/30</span></h3>
            </div>
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">payments</span>
                    </div>
                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-[9px] font-bold rounded uppercase">Partiel</span>
                </div>
                <p class="text-navy-700 text-xs font-medium">Solde Restant</p>
                <h3 class="text-lg md:text-xl font-bold text-navy-900 mt-0.5">50 000 <span class="text-sm font-semibold">FCFA</span></h3>
            </div>
        </div>

        <!-- Notes par matière + Infos financières -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Notes par matière -->
            <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Notes — Semestre 1</h2>
                        <p class="text-xs text-navy-700">Moyennes par matière</p>
                    </div>
                    <a href="#" class="text-sm font-semibold text-primary hover:underline">Détail</a>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-navy-700 w-28 shrink-0 truncate">Mathématiques</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="bg-emerald-500 h-2 rounded-full" style="width:82%"></div>
                        </div>
                        <span class="text-xs font-bold text-navy-900 w-10 text-right shrink-0">16,4</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-navy-700 w-28 shrink-0 truncate">Français</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width:60%"></div>
                        </div>
                        <span class="text-xs font-bold text-navy-900 w-10 text-right shrink-0">12,0</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-navy-700 w-28 shrink-0 truncate">Physique-Chimie</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width:55%"></div>
                        </div>
                        <span class="text-xs font-bold text-navy-900 w-10 text-right shrink-0">11,0</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-navy-700 w-28 shrink-0 truncate">Histoire-Géo</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="bg-violet-500 h-2 rounded-full" style="width:65%"></div>
                        </div>
                        <span class="text-xs font-bold text-navy-900 w-10 text-right shrink-0">13,0</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-navy-700 w-28 shrink-0 truncate">SVT</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="bg-emerald-400 h-2 rounded-full" style="width:70%"></div>
                        </div>
                        <span class="text-xs font-bold text-navy-900 w-10 text-right shrink-0">14,0</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-navy-700 w-28 shrink-0 truncate">Anglais</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2">
                            <div class="bg-orange-400 h-2 rounded-full" style="width:45%"></div>
                        </div>
                        <span class="text-xs font-bold text-navy-900 w-10 text-right shrink-0">9,0</span>
                    </div>
                </div>
            </div>

            <!-- Suivi financier -->
            <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-base md:text-lg font-bold text-navy-900 mb-5">Suivi Financier</h2>

                <!-- Barre de progression paiement -->
                <div class="mb-5">
                    <div class="flex justify-between mb-1.5">
                        <span class="text-xs font-semibold text-navy-700">Paiement effectué</span>
                        <span class="text-xs font-bold text-navy-900">66%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full" style="width:66%"></div>
                    </div>
                    <div class="flex justify-between mt-1.5">
                        <span class="text-[10px] text-navy-700">100 000 FCFA payés</span>
                        <span class="text-[10px] text-navy-700">150 000 FCFA total</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-xs text-navy-700">Total dû</span>
                        <span class="text-xs font-bold text-navy-900">150 000 FCFA</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-xs text-navy-700">Total payé</span>
                        <span class="text-xs font-bold text-emerald-600">100 000 FCFA</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-navy-700">Solde restant</span>
                        <span class="text-xs font-bold text-orange-500">50 000 FCFA</span>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-orange-50 rounded-xl border border-orange-100">
                    <p class="text-xs font-semibold text-orange-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">info</span>
                        Solde restant à régler
                    </p>
                </div>
            </div>
        </div>

        <!-- Derniers bulletins -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Bulletins</h2>
                <a href="#" class="text-sm font-semibold text-primary hover:underline">Voir tout</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="min-width:400px;">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Période</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Moyenne</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Rang</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Bulletin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 md:px-6 py-3 text-sm font-medium whitespace-nowrap">Semestre 1 — 2024/2025</td>
                            <td class="px-4 md:px-6 py-3">
                                <span class="text-sm font-bold text-emerald-600">12,4 / 20</span>
                            </td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">5ème / 38</td>
                            <td class="px-4 md:px-6 py-3 text-right">
                                <a href="#" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline whitespace-nowrap">
                                    <span class="material-symbols-outlined text-sm">download</span>Télécharger
                                </a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 md:px-6 py-3 text-sm font-medium whitespace-nowrap">Annuel — 2023/2024</td>
                            <td class="px-4 md:px-6 py-3">
                                <span class="text-sm font-bold text-emerald-600">11,8 / 20</span>
                            </td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">8ème / 40</td>
                            <td class="px-4 md:px-6 py-3 text-right">
                                <a href="#" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline whitespace-nowrap">
                                    <span class="material-symbols-outlined text-sm">download</span>Télécharger
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
</body>
</html>