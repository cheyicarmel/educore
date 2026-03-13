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

    {{-- KPIs --}}
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

    {{-- Performances + Paiements récents --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Performances par classe --}}
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="mb-4">
                <h2 class="text-base md:text-lg font-bold text-navy-900">Performances par Classe</h2>
                <p class="text-xs text-navy-700">Taux de réussite annuel — élèves passants</p>
            </div>
            @if($performancesClasses->count() > 0)
            <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                @foreach($performancesClasses as $perf)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-navy-900">{{ $perf['nom'] }}</span>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-400">{{ $perf['passants'] }}/{{ $perf['moyennes_calcs'] }} passants</span>
                            <span class="text-xs font-extrabold {{ $perf['taux_reussite'] >= 50 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $perf['taux_reussite'] }}%
                            </span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all {{ $perf['taux_reussite'] >= 50 ? 'bg-emerald-400' : 'bg-rose-400' }}"
                            style="width: {{ $perf['taux_reussite'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="h-44 flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-slate-300 text-4xl">bar_chart</span>
                <p class="text-sm font-semibold text-slate-400">Disponible après calcul des moyennes annuelles</p>
            </div>
            @endif
        </div>

        {{-- Paiements récents --}}
        <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-navy-900">Paiements Récents</h2>
                <a href="{{ route('admin.finances.index') }}" class="text-xs font-semibold text-primary hover:underline">Voir tout</a>
            </div>
            @if($derniersPaiements->count() > 0)
            <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                @foreach($derniersPaiements as $paiement)
                @php $eleve = $paiement->inscription?->eleve?->user; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="text-[10px] font-bold text-primary">
                            {{ strtoupper(substr($eleve?->prenom ?? '?', 0, 1) . substr($eleve?->nom ?? '?', 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-navy-900 truncate">{{ $eleve?->prenom }} {{ $eleve?->nom }}</p>
                        <p class="text-[10px] text-slate-400">{{ $paiement->inscription?->classe?->nom }} · {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-xs font-extrabold text-emerald-600 shrink-0">
                        {{ number_format($paiement->montant, 0, ',', ' ') }} F
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="h-44 flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-slate-300 text-4xl">payments</span>
                <p class="text-sm font-semibold text-slate-400">Aucun paiement enregistré</p>
            </div>
            @endif
        </div>

    </div>

    {{-- Derniers paiements tableau --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-navy-900">Derniers Paiements</h2>
            <a href="{{ route('admin.finances.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                Voir les finances
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        @if($derniersPaiements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width: 600px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Montant</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Mode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($derniersPaiements as $paiement)
                    @php $eleve = $paiement->inscription?->eleve?->user; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($eleve?->prenom ?? '?', 0, 1) . substr($eleve?->nom ?? '?', 0, 1)) }}
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-navy-900">{{ $eleve?->prenom }} {{ $eleve?->nom }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-navy-700">{{ $paiement->inscription?->classe?->nom }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-sm font-extrabold text-emerald-600">{{ number_format($paiement->montant, 0, ',', ' ') }} F</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-navy-700">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full">
                                {{ ucfirst($paiement->mode_paiement ?? '—') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-4xl">payments</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucun paiement enregistré</p>
        </div>
        @endif
    </div>

</div>
@endsection