<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tableau de Bord Enseignant - EduCore</title>
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
            <span class="material-symbols-outlined shrink-0">groups</span>
            <span class="lbl">Mes Classes</span>
            <span class="tip">Mes Classes</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">edit_note</span>
            <span class="lbl">Saisie des Notes</span>
            <span class="tip">Saisie des Notes</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">calculate</span>
            <span class="lbl">Moyennes</span>
            <span class="tip">Moyennes</span>
        </a>
        <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
            <span class="material-symbols-outlined shrink-0">description</span>
            <span class="lbl">Bulletins</span>
            <span class="tip">Bulletins</span>
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
            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">AK</div>
            <div class="user-info min-w-0">
                <p class="text-sm font-semibold truncate">Afi Koffi</p>
                <p class="text-xs text-navy-700 truncate">Enseignant</p>
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
            <div class="relative hidden md:block">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input class="pl-9 pr-4 py-2 bg-slate-50 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary/20 focus:outline-none" placeholder="Rechercher un élève..." type="text"/>
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
                <span class="material-symbols-outlined text-primary">school</span>
            </div>
        </div>
    </header>

    <div class="p-4 md:p-6 lg:p-8 space-y-6">

        <!-- Heading -->
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Tableau de Bord</h1>
            <p class="text-sm text-navy-700 mt-1">Bienvenue, Afi Koffi — voici vos classes et activités.</p>
        </div>

        <!-- KPI -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">groups</span>
                    </div>
                </div>
                <p class="text-navy-700 text-xs font-medium">Mes Classes</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">4</h3>
            </div>
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">person</span>
                    </div>
                </div>
                <p class="text-navy-700 text-xs font-medium">Total Élèves</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">148</h3>
            </div>
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">check_circle</span>
                    </div>
                </div>
                <p class="text-navy-700 text-xs font-medium">Notes Saisies</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">320</h3>
            </div>
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                        <span class="material-symbols-outlined text-xl">pending</span>
                    </div>
                    <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-bold rounded uppercase">À faire</span>
                </div>
                <p class="text-navy-700 text-xs font-medium">Notes Manquantes</p>
                <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">28</h3>
            </div>
        </div>

        <!-- Mes classes + Activité récente -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Mes classes -->
            <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Classes</h2>
                        <p class="text-xs text-navy-700">Matière enseignée : Mathématiques</p>
                    </div>
                    <a href="#" class="text-sm font-semibold text-primary hover:underline">Voir tout</a>
                </div>
                <div class="space-y-3">
                    <!-- Classe 1 -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-primary text-lg">groups</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate">4ème C</p>
                                <p class="text-xs text-navy-700">38 élèves · Prof principal</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-navy-900">12,4</p>
                                <p class="text-[10px] text-navy-700">Moy. classe</p>
                            </div>
                            <a href="#" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                    <!-- Classe 2 -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-indigo-500 text-lg">groups</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate">3ème L</p>
                                <p class="text-xs text-navy-700">35 élèves</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-navy-900">10,8</p>
                                <p class="text-[10px] text-navy-700">Moy. classe</p>
                            </div>
                            <a href="#" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                    <!-- Classe 3 -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">groups</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate">Tle D</p>
                                <p class="text-xs text-navy-700">40 élèves</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-navy-900">13,2</p>
                                <p class="text-[10px] text-navy-700">Moy. classe</p>
                            </div>
                            <a href="#" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                    <!-- Classe 4 -->
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-violet-500 text-lg">groups</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold truncate">2nde A</p>
                                <p class="text-xs text-navy-700">35 élèves</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-bold text-navy-900">11,5</p>
                                <p class="text-[10px] text-navy-700">Moy. classe</p>
                            </div>
                            <a href="#" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-base md:text-lg font-bold text-navy-900 mb-5">Activité Récente</h2>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-emerald-600 text-sm">edit_note</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold">Notes saisies</p>
                            <p class="text-xs text-navy-700 truncate">Interro 2 — 4ème C</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Il y a 1 heure</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-blue-600 text-sm">calculate</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold">Moyennes calculées</p>
                            <p class="text-xs text-navy-700 truncate">Semestre 1 — Tle D</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Hier</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-indigo-600 text-sm">edit_note</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold">Notes saisies</p>
                            <p class="text-xs text-navy-700 truncate">Devoir 1 — 3ème L</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Hier</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-violet-600 text-sm">description</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold">Bulletins générés</p>
                            <p class="text-xs text-navy-700 truncate">Semestre 1 — 4ème C</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Il y a 2 jours</p>
                        </div>
                    </div>
                </div>
                <button class="w-full mt-5 py-2 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    Voir tout l'historique
                </button>
            </div>
        </div>

        <!-- Table : Notes récentes -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-base md:text-lg font-bold text-navy-900">Dernières Notes Saisies</h2>
                <a href="#" class="flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                    Saisir des notes
                    <span class="material-symbols-outlined text-sm">add</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="min-width:520px;">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Type</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Semestre</th>
                            <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Note /20</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 md:px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">KM</div>
                                    <span class="text-sm font-medium whitespace-nowrap">Kofi Mensah</span>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">4ème C</td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Interrogation 2</td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Semestre 1</td>
                            <td class="px-4 md:px-6 py-3 text-right">
                                <span class="text-sm font-bold text-emerald-600">16,5</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 md:px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">AA</div>
                                    <span class="text-sm font-medium whitespace-nowrap">Ama Adjobi</span>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">4ème C</td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Interrogation 2</td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Semestre 1</td>
                            <td class="px-4 md:px-6 py-3 text-right">
                                <span class="text-sm font-bold text-orange-500">9,0</span>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 md:px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">YA</div>
                                    <span class="text-sm font-medium whitespace-nowrap">Yao Agbodjan</span>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Tle D</td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Devoir 1</td>
                            <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Semestre 1</td>
                            <td class="px-4 md:px-6 py-3 text-right">
                                <span class="text-sm font-bold text-emerald-600">14,0</span>
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