<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tableau de Bord Administrateur - EduCore</title>
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

        /* ── SIDEBAR ── */
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

        /* Collapsed (icônes seulement) — visible md+ */
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
        #sidebar.collapsed nav {
            overflow: visible;
        }

        /* Tooltip */
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

        /* Mobile : caché par défaut */
        @media (max-width: 767px) {
            #sidebar { transform: translateX(-100%); width: 17rem !important; }
            #sidebar.open { transform: translateX(0); }
        }

        /* Active state */
        .nav-link.active {
            background: rgba(43,108,238,0.1);
            color: #2b6cee;
            border-right: 3px solid #2b6cee;
        }

        /* ── OVERLAY ── */
        #overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 40;
        }
        #overlay.show { display: block; }

        /* ── MAIN ── */
        #main {
            margin-left: 17rem;
            min-width: 0;
            transition: margin-left 0.25s ease;
        }
        #main.collapsed { margin-left: 4.5rem; }
        @media (max-width: 767px) {
            #main { margin-left: 0 !important; }
        }
    </style>
</head>
<body class="bg-background-light text-slate-900 min-h-screen">

    <!-- Overlay mobile -->
    <div id="overlay"></div>

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside id="sidebar">

        <!-- Logo -->
        <div class="flex items-center gap-3 p-5 shrink-0">
            <div class="bg-primary p-2 rounded-xl text-white shrink-0">
                <span class="material-symbols-outlined text-white text-2xl">school</span>
            </div>
            <span class="logo-text text-xl font-bold text-navy-900">EduCore</span>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">
            <a href="#" class="nav-link active flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">dashboard</span>
                <span class="lbl">Tableau de bord</span>
                <span class="tip">Tableau de bord</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">calendar_today</span>
                <span class="lbl">Années Académiques</span>
                <span class="tip">Années Académiques</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">groups</span>
                <span class="lbl">Classes</span>
                <span class="tip">Classes</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">menu_book</span>
                <span class="lbl">Matières</span>
                <span class="tip">Matières</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">category</span>
                <span class="lbl">Séries</span>
                <span class="tip">Séries</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">person_pin_circle</span>
                <span class="lbl">Enseignants</span>
                <span class="tip">Enseignants</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">school</span>
                <span class="lbl">Élèves</span>
                <span class="tip">Élèves</span>
            </a>
            <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                <span class="material-symbols-outlined shrink-0">payments</span>
                <span class="lbl">Finances</span>
                <span class="tip">Finances</span>
            </a>

            <!-- Section Super Admin -->
            <div class="pt-3 mt-2 border-t border-slate-100">
                <p class="section-label px-4 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Super Admin</p>
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined shrink-0">admin_panel_settings</span>
                    <span class="lbl">Administrateurs</span>
                    <span class="tip">Administrateurs</span>
                </a>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <a href="#" class="nav-link flex items-center gap-3 px-4 py-3 text-navy-700 hover:bg-slate-50 rounded-lg font-medium text-sm transition-colors">
                    <span class="material-symbols-outlined shrink-0">settings</span>
                    <span class="lbl">Paramètres</span>
                    <span class="tip">Paramètres</span>
                </a>
            </div>
        </nav>

        <!-- User + Logout -->
        <div class="p-4 border-t border-slate-100 shrink-0">
            <div class="flex items-center gap-3 p-2">
                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs shrink-0">JD</div>
                <div class="user-info min-w-0">
                    <p class="text-sm font-semibold truncate">Jean Dupont</p>
                    <p class="text-xs text-navy-700 truncate">Super Admin</p>
                </div>
            </div>
            <button class="logout-btn w-full mt-2 flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-base shrink-0">logout</span>
                <span class="logout-text">Déconnexion</span>
            </button>
        </div>
    </aside>

    <!-- ═══════════════ MAIN ═══════════════ -->
    <div id="main">

        <!-- Top Bar -->
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <!-- Hamburger mobile -->
                <button id="hamburger" class="md:hidden p-2 text-slate-500 hover:bg-slate-50 rounded-lg">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <!-- Toggle collapse desktop -->
                <button id="toggle-collapse" class="hidden md:flex p-2 text-slate-500 hover:bg-slate-50 rounded-lg">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <!-- Search — caché sur mobile -->
                <div class="relative hidden md:block">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input class="pl-9 pr-4 py-2 bg-slate-50 border-none rounded-lg text-sm w-64 focus:ring-2 focus:ring-primary/20 focus:outline-none" placeholder="Rechercher..." type="text"/>
                </div>
            </div>
            <!-- Droite -->
            <div class="flex items-center gap-2">
                <!-- Notifs — caché sur mobile -->
                <button class="hidden md:flex p-2 text-slate-500 hover:bg-slate-50 rounded-full relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Année académique — desktop uniquement -->
                <div class="hidden lg:flex flex-col text-right ml-2">
                    <p class="text-xs font-semibold text-navy-900">Année 2024-2025</p>
                    <p class="text-[10px] text-navy-700 uppercase tracking-wide">Semestre 1 · Active</p>
                </div>

                <!-- Logo — mobile uniquement -->
                <div class="md:hidden flex items-center gap-2">
                    <div class="p-2 rounded-xl text-white shrink-0">
                <span class="material-symbols-outlined text-primary w-4 h-4">school</span>
            </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="p-4 md:p-6 lg:p-8 space-y-6">

            <!-- Heading -->
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Tableau de Bord</h1>
                <p class="text-sm text-navy-700 mt-1">Bienvenue, voici un aperçu de l'activité scolaire.</p>
            </div>

            <!-- KPI Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">

                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <span class="material-symbols-outlined text-xl">person</span>
                        </div>
                        <span class="text-emerald-500 text-xs font-semibold flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-xs">trending_up</span>+2.5%
                        </span>
                    </div>
                    <p class="text-navy-700 text-xs font-medium">Total Élèves</p>
                    <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">1 240</h3>
                </div>

                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <span class="material-symbols-outlined text-xl">co_present</span>
                        </div>
                        <span class="text-emerald-500 text-xs font-semibold flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-xs">trending_up</span>+3
                        </span>
                    </div>
                    <p class="text-navy-700 text-xs font-medium">Enseignants Actifs</p>
                    <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">85</h3>
                </div>

                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                        </div>
                        <span class="text-emerald-500 text-xs font-semibold flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-xs">trending_up</span>+5.4%
                        </span>
                    </div>
                    <p class="text-navy-700 text-xs font-medium">Revenus (Ce mois)</p>
                    <h3 class="text-lg md:text-xl font-bold text-navy-900 mt-0.5">4 500 000 <span class="text-sm font-semibold">FCFA</span></h3>
                </div>

                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                            <span class="material-symbols-outlined text-xl">error</span>
                        </div>
                        <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-bold rounded uppercase">Urgent</span>
                    </div>
                    <p class="text-navy-700 text-xs font-medium">Retards de Paiement</p>
                    <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">12</h3>
                </div>
            </div>

            <!-- Charts + Activité -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- Graphique -->
                <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-start justify-between mb-5 gap-2">
                        <div>
                            <h2 class="text-base md:text-lg font-bold text-navy-900">Performances Globales</h2>
                            <p class="text-xs text-navy-700">Taux de réussite moyen par niveau (%)</p>
                        </div>
                        <select class="bg-slate-50 border-none rounded-lg text-xs font-medium py-1.5 pl-2 pr-6 text-navy-700 shrink-0">
                            <option>Semestre 1</option>
                            <option>Semestre 2</option>
                            <option>Année</option>
                        </select>
                    </div>
                    <div class="h-44 md:h-56 flex items-end justify-between gap-2">
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">85%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:85%"></div>
                            <span class="text-[10px] font-bold text-navy-700">6ème</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">72%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:72%"></div>
                            <span class="text-[10px] font-bold text-navy-700">5ème</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">94%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:94%"></div>
                            <span class="text-[10px] font-bold text-navy-700">4ème</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">65%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:65%"></div>
                            <span class="text-[10px] font-bold text-navy-700">3ème</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">78%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:78%"></div>
                            <span class="text-[10px] font-bold text-navy-700">2nde</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">88%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:88%"></div>
                            <span class="text-[10px] font-bold text-navy-700">1ère</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center gap-1.5 group">
                            <span class="text-[10px] font-bold text-primary opacity-0 group-hover:opacity-100 transition-opacity">91%</span>
                            <div class="w-full bg-primary rounded-t-md group-hover:bg-primary/80 transition-colors" style="height:91%"></div>
                            <span class="text-[10px] font-bold text-navy-700">Tle</span>
                        </div>
                    </div>
                </div>

                <!-- Activité récente -->
                <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-base md:text-lg font-bold text-navy-900 mb-5">Activité Récente</h2>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-blue-600 text-sm">person_add</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">Nouvel élève inscrit</p>
                                <p class="text-xs text-navy-700 truncate">Kofi Mensah — 4ème C</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Il y a 2 heures</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-emerald-600 text-sm">receipt_long</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">Paiement enregistré</p>
                                <p class="text-xs text-navy-700 truncate">Frais scolaires — Ama Adjobi</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Il y a 4 heures</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-indigo-600 text-sm">calculate</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">Moyennes calculées</p>
                                <p class="text-xs text-navy-700 truncate">Semestre 1 — 3ème A</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Hier</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-violet-600 text-sm">description</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold">Bulletins générés</p>
                                <p class="text-xs text-navy-700 truncate">Semestre 1 — Terminale D</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 uppercase">Hier</p>
                            </div>
                        </div>
                    </div>
                    <button class="w-full mt-5 py-2 text-sm font-bold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        Voir tout l'historique
                    </button>
                </div>
            </div>

            <!-- Table : Derniers Paiements -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-base md:text-lg font-bold text-navy-900">Derniers Paiements</h2>
                    <button class="flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                        Exporter CSV
                        <span class="material-symbols-outlined text-sm">download</span>
                    </button>
                </div>
                <!-- Scroll horizontal uniquement sur le tableau -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="min-width: 560px;">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                                <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                                <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Date</th>
                                <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Mode</th>
                                <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Statut</th>
                                <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Montant</th>
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
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">12 Jan. 2025</td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Mobile Money</td>
                                <td class="px-4 md:px-6 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-600 bg-emerald-100 rounded uppercase whitespace-nowrap">Complété</span>
                                </td>
                                <td class="px-4 md:px-6 py-3 text-sm font-bold text-right whitespace-nowrap">75 000 FCFA</td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 md:px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">AA</div>
                                        <span class="text-sm font-medium whitespace-nowrap">Ama Adjobi</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">3ème L</td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">11 Jan. 2025</td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Espèces</td>
                                <td class="px-4 md:px-6 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-orange-600 bg-orange-100 rounded uppercase whitespace-nowrap">Partiel</span>
                                </td>
                                <td class="px-4 md:px-6 py-3 text-sm font-bold text-right whitespace-nowrap">50 000 FCFA</td>
                            </tr>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 md:px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">YA</div>
                                        <span class="text-sm font-medium whitespace-nowrap">Yao Agbodjan</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Tle D</td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">10 Jan. 2025</td>
                                <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Virement</td>
                                <td class="px-4 md:px-6 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-600 bg-emerald-100 rounded uppercase whitespace-nowrap">Complété</span>
                                </td>
                                <td class="px-4 md:px-6 py-3 text-sm font-bold text-right whitespace-nowrap">120 000 FCFA</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Footer -->
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

        // Desktop : collapse/expand
        toggleCollapse.addEventListener('click', () => {
            const isCollapsed = sidebar.classList.toggle('collapsed');
            main.classList.toggle('collapsed', isCollapsed);
        });

        // Mobile : open/close
        hamburger.addEventListener('click', () => {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        // Auto-collapse entre 768 et 1024px
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
    </script>
</body>
</html>