@extends('layouts.comptable')

@section('title', 'Tableau de Bord — EduCore Comptable')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Tableau de Bord</h1>
            <p class="text-sm text-navy-700 mt-1">Bonjour, {{ Auth::user()->prenom }} — voici le suivi financier de <span class="font-semibold">{{ $anneeActive?->libelle }}</span>.</p>
        </div>
        <a href="{{ route('comptable.paiements.create') }}"
            class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>
            <span class="hidden sm:inline">Nouveau Paiement</span>
        </a>
    </div>

    {{-- KPIs ligne 1 --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        {{-- Total Encaissé --}}
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full uppercase">Encaissé</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Total Encaissé</p>
            <h3 class="text-base md:text-lg font-extrabold text-navy-900 mt-0.5">
                {{ number_format($totalPaye, 0, ',', ' ') }} <span class="text-xs font-semibold text-navy-700">FCFA</span>
            </h3>
        </div>
        {{-- Total Dû --}}
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">request_quote</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Total Dû</p>
            <h3 class="text-base md:text-lg font-extrabold text-navy-900 mt-0.5">
                {{ number_format($totalDu, 0, ',', ' ') }} <span class="text-xs font-semibold text-navy-700">FCFA</span>
            </h3>
        </div>
        {{-- Solde Restant --}}
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">pending</span>
                </div>
                @if($totalSolde > 0)
                <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full uppercase">Restant</span>
                @endif
            </div>
            <p class="text-navy-700 text-xs font-medium">Soldes Restants</p>
            <h3 class="text-base md:text-lg font-extrabold text-navy-900 mt-0.5">
                {{ number_format($totalSolde, 0, ',', ' ') }} <span class="text-xs font-semibold text-navy-700">FCFA</span>
            </h3>
        </div>
        {{-- Taux Recouvrement --}}
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-violet-50 text-violet-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">donut_large</span>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 {{ $tauxRecouvrement >= 80 ? 'bg-emerald-100 text-emerald-700' : ($tauxRecouvrement >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }} rounded-full uppercase">
                    {{ $tauxRecouvrement >= 80 ? 'Bon' : ($tauxRecouvrement >= 50 ? 'Moyen' : 'Faible') }}
                </span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Taux de Recouvrement</p>
            <h3 class="text-2xl font-extrabold text-navy-900 mt-0.5">{{ $tauxRecouvrement }}<span class="text-sm font-semibold text-navy-700">%</span></h3>
        </div>
    </div>

    {{-- KPIs ligne 2 --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-primary/10 text-primary rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">today</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Paiements Aujourd'hui</p>
            <h3 class="text-2xl font-extrabold text-navy-900 mt-0.5">{{ $paiementsAujourdhui }}</h3>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-teal-50 text-teal-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">payments</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Encaissé Aujourd'hui</p>
            <h3 class="text-base font-extrabold text-navy-900 mt-0.5">{{ number_format($encaisseAujourdhui, 0, ',', ' ') }} <span class="text-xs font-semibold text-navy-700">FCFA</span></h3>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">calendar_month</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Encaissé Ce Mois</p>
            <h3 class="text-base font-extrabold text-navy-900 mt-0.5">{{ number_format($encaisseMois, 0, ',', ' ') }} <span class="text-xs font-semibold text-navy-700">FCFA</span></h3>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-rose-50 text-rose-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">warning</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Élèves en Retard</p>
            <h3 class="text-2xl font-extrabold text-navy-900 mt-0.5">{{ $elevesRetard }}</h3>
        </div>
    </div>

    {{-- Graphiques ligne 1 : Area chart + Donut --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Area Chart — Encaissements 12 mois --}}
        <div class="lg:col-span-2 bg-white p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between mb-5 gap-2">
                <div>
                    <h2 class="text-base font-bold text-navy-900">Évolution des Encaissements</h2>
                    <p class="text-xs text-navy-700 mt-0.5">12 derniers mois (FCFA)</p>
                </div>
            </div>
            <div class="h-52 md:h-64">
                <canvas id="areaChart"></canvas>
            </div>
        </div>

        {{-- Donut — Statuts élèves --}}
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-navy-900 mb-1">Statuts des Élèves</h2>
            <p class="text-xs text-navy-700 mb-4">Répartition financière — {{ $totalEleves }} élèves</p>
            <div class="h-44 flex items-center justify-center">
                <canvas id="donutChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                        <span class="text-navy-700 font-medium">Soldés</span>
                    </div>
                    <span class="font-bold text-navy-900">{{ $elevesAJour }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500 shrink-0"></span>
                        <span class="text-navy-700 font-medium">En retard</span>
                    </div>
                    <span class="font-bold text-navy-900">{{ $elevesRetard }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques ligne 2 : Horizontal bar + Progress ring --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Horizontal Bar — Top classes --}}
        <div class="lg:col-span-2 bg-white p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-navy-900 mb-1">Top Classes par Encaissement</h2>
            <p class="text-xs text-navy-700 mb-5">Montants encaissés par classe</p>
            <div class="h-52 md:h-64">
                <canvas id="hbarChart"></canvas>
            </div>
        </div>

        {{-- Progress Ring — Taux recouvrement + modes --}}
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-navy-900 mb-1">Recouvrement Global</h2>
            <p class="text-xs text-navy-700 mb-4">Progression vers l'objectif 100%</p>

            {{-- Ring animé SVG --}}
            <div class="flex items-center justify-center my-2">
                @php
                    $r = 54; $circ = 2 * M_PI * $r;
                    $offset = $circ * (1 - $tauxRecouvrement / 100);
                @endphp
                <div class="relative w-36 h-36">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="{{ $r }}" fill="none" stroke="#f1f5f9" stroke-width="12"/>
                        <circle cx="60" cy="60" r="{{ $r }}" fill="none"
                            stroke="{{ $tauxRecouvrement >= 80 ? '#10b981' : ($tauxRecouvrement >= 50 ? '#f59e0b' : '#f43f5e') }}"
                            stroke-width="12" stroke-linecap="round"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $offset }}"
                            style="transition: stroke-dashoffset 1s ease"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-navy-900">{{ $tauxRecouvrement }}%</span>
                        <span class="text-[10px] text-navy-700 font-semibold uppercase tracking-wide">Recouvré</span>
                    </div>
                </div>
            </div>

            {{-- Modes de paiement --}}
            <div class="mt-4 space-y-2">
                <p class="text-xs font-bold text-navy-700 uppercase tracking-wider mb-2">Modes de paiement</p>
                @forelse($parMode as $mode)
                <div class="flex items-center justify-between text-xs p-2 bg-slate-50 rounded-lg">
                    <span class="text-navy-700 font-medium capitalize">{{ $mode->mode_paiement ?? 'Non renseigné' }}</span>
                    <div class="text-right">
                        <span class="font-bold text-navy-900">{{ $mode->nb }}</span>
                        <span class="text-navy-700"> pmt</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-2">Aucun paiement</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Retards + Derniers paiements --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Retards --}}
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-navy-900">Retards de Paiement</h2>
                <span class="px-2.5 py-1 bg-rose-100 text-rose-700 text-[10px] font-bold rounded-full uppercase">{{ $elevesRetard }} élèves</span>
            </div>
            @forelse($retards as $r)
            @php $eleve = $r->inscription->eleve->user; @endphp
            <div class="flex items-center justify-between p-3 bg-rose-50 rounded-xl mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-[10px] font-bold text-rose-600 shrink-0">
                        {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold truncate">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                        <p class="text-[10px] text-navy-700">{{ $r->inscription->classe->nom }}</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-rose-600 shrink-0 ml-2">
                    {{ number_format($r->solde_restant, 0, ',', ' ') }}
                </span>
            </div>
            @empty
            <div class="text-center py-6">
                <span class="material-symbols-outlined text-emerald-300 text-4xl">check_circle</span>
                <p class="text-xs font-semibold text-slate-400 mt-2">Aucun retard</p>
            </div>
            @endforelse
            @if($elevesRetard > 5)
            <a href="{{ route('comptable.retards') }}"
                class="w-full mt-2 py-2 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors flex items-center justify-center gap-1">
                Voir tous les retards
            </a>
            @endif
        </div>

        {{-- Derniers paiements --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
                <h2 class="text-base font-bold text-navy-900">Derniers Paiements</h2>
                <a href="{{ route('comptable.paiements.index') }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                    Voir tout <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="min-width:480px;">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Mode</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($derniersPaiements as $p)
                        @php $eleve = $p->inscription->eleve->user; @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">
                                        {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-semibold whitespace-nowrap">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-navy-700 whitespace-nowrap">{{ $p->inscription->classe->nom }}</td>
                            <td class="px-4 py-3 text-xs text-navy-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d M. Y') }}</td>
                            <td class="px-4 py-3 text-xs text-navy-700 whitespace-nowrap capitalize">{{ $p->mode_paiement ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs font-bold text-right whitespace-nowrap">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Aucun paiement enregistré.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const moisLabels = @json($moisLabels);
const moisData   = @json($moisData);
const topClasses = @json($topClasses->pluck('classe'));
const topTotaux  = @json($topClasses->pluck('total'));

// ── Area Chart ─────────────────────────────────────────────────────
const ctxArea = document.getElementById('areaChart').getContext('2d');
const gradArea = ctxArea.createLinearGradient(0, 0, 0, 300);
gradArea.addColorStop(0, 'rgba(43,108,238,0.25)');
gradArea.addColorStop(1, 'rgba(43,108,238,0.01)');

new Chart(ctxArea, {
    type: 'line',
    data: {
        labels: moisLabels,
        datasets: [{
            label: 'Encaissements',
            data: moisData,
            borderColor: '#2b6cee',
            backgroundColor: gradArea,
            borderWidth: 2.5,
            fill: true,
            tension: 0.45,
            pointBackgroundColor: '#2b6cee',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + new Intl.NumberFormat('fr-FR').format(ctx.parsed.y) + ' FCFA'
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { family: 'Lexend', size: 10 }, color: '#4c669a' } },
            y: { grid: { color: '#f1f5f9' },
                ticks: {
                    font: { family: 'Lexend', size: 10 }, color: '#4c669a',
                    callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'k' : v
                }
            }
        }
    }
});

// ── Donut Chart ────────────────────────────────────────────────────
new Chart(document.getElementById('donutChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Soldés', 'En retard'],
        datasets: [{
            data: [{{ $elevesAJour }}, {{ $elevesRetard }}],
            backgroundColor: ['#10b981', '#f43f5e'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ' : ' + ctx.parsed } }
        }
    }
});

// ── Horizontal Bar Chart ───────────────────────────────────────────
new Chart(document.getElementById('hbarChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: topClasses,
        datasets: [{
            label: 'Encaissé',
            data: topTotaux,
            backgroundColor: [
                'rgba(43,108,238,0.85)', 'rgba(16,185,129,0.85)', 'rgba(245,158,11,0.85)',
                'rgba(139,92,246,0.85)', 'rgba(20,184,166,0.85)', 'rgba(244,63,94,0.85)'
            ],
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + new Intl.NumberFormat('fr-FR').format(ctx.parsed.x) + ' FCFA'
                }
            }
        },
        scales: {
            x: { grid: { color: '#f1f5f9' },
                ticks: {
                    font: { family: 'Lexend', size: 10 }, color: '#4c669a',
                    callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'k' : v
                }
            },
            y: { grid: { display: false }, ticks: { font: { family: 'Lexend', size: 11 }, color: '#0d121b' } }
        }
    }
});
</script>
@endsection