@extends('layouts.admin')

@section('title', 'Tableau de Bord — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Heading --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Tableau de Bord</h1>
            <p class="text-sm text-navy-700 mt-1">
                Bienvenue, voici un aperçu de l'activité scolaire.
                @if($anneeActive)
                <span class="font-semibold text-primary">{{ $anneeActive->libelle }}</span>
                @endif
            </p>
        </div>
        @if(!$anneeActive)
        <div class="inline-flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
            <span class="material-symbols-outlined text-amber-500 text-sm">warning</span>
            <p class="text-xs font-semibold text-amber-700">Aucune année académique active</p>
        </div>
        @endif
    </div>

    {{-- KPI Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">person</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Total Élèves</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ number_format($totalEleves, 0, ',', ' ') }}</h3>
            <p class="text-xs text-slate-400 mt-1">Inscrits cette année</p>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">co_present</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Enseignants Actifs</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ $totalEnseignants }}</h3>
            <p class="text-xs text-slate-400 mt-1">Comptes actifs</p>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-violet-50 text-violet-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">school</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Classes</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ $totalClasses }}</h3>
            <p class="text-xs text-slate-400 mt-1">Cette année</p>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">error</span>
                </div>
                @if($retardsPaiement > 0)
                <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-bold rounded uppercase">Urgent</span>
                @endif
            </div>
            <p class="text-navy-700 text-xs font-medium">Retards de Paiement</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ number_format($retardsPaiement, 0, ',', ' ') }}</h3>
            <p class="text-xs text-slate-400 mt-1">Élèves en retard</p>
        </div>

    </div>

    {{-- Graphique + Activité --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Placeholder graphique performances --}}
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between mb-2 gap-2">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-navy-900">Performances Globales</h2>
                    <p class="text-xs text-navy-700">Taux de réussite moyen par niveau</p>
                </div>
            </div>
            <div class="h-44 md:h-56 flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-slate-300 text-4xl">bar_chart</span>
                <div class="text-center">
                    <p class="text-sm font-semibold text-slate-400">Disponible après saisie des notes</p>
                    <p class="text-xs text-slate-300 mt-1">Les taux de réussite s'afficheront ici une fois les moyennes calculées.</p>
                </div>
            </div>
        </div>

        {{-- Placeholder activité récente --}}
        <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-base md:text-lg font-bold text-navy-900 mb-2">Activité Récente</h2>
            <div class="h-44 md:h-56 flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-slate-300 text-4xl">history</span>
                <div class="text-center">
                    <p class="text-sm font-semibold text-slate-400">Aucune activité récente</p>
                    <p class="text-xs text-slate-300 mt-1">Les dernières actions apparaîtront ici.</p>
                </div>
            </div>
        </div>

    </div>

    {{-- Placeholder derniers paiements --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-navy-900">Derniers Paiements</h2>
            <a href="{{ route('admin.finances.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                Voir les finances
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-4xl">payments</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Disponible après enregistrement des paiements</p>
            <p class="text-xs text-slate-300 mt-1">Les derniers paiements enregistrés par le comptable apparaîtront ici.</p>
        </div>
    </div>

</div>
@endsection