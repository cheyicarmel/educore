@extends('layouts.admin')

@section('title', 'Tableau de Bord — EduCore')

@section('content')
<div class="space-y-6">

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
@endsection