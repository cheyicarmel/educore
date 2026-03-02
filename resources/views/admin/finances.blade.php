@extends('layouts.admin')

@section('title', 'Finances — EduCore')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Finances</h1>
        <p class="text-sm text-navy-700 mt-1">Vue d'ensemble de la situation financière de l'établissement.</p>
    </div>
    <form method="GET" action="{{ route('admin.finances.index') }}" class="shrink-0">
        <select name="annee_id" onchange="this.form.submit()"
            class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm text-navy-900 font-semibold focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all shadow-sm">
            @foreach($annees as $annee)
            <option value="{{ $annee->id }}" {{ $anneeSelectionnee?->id == $annee->id ? 'selected' : '' }}>
                {{ $annee->libelle }}{{ $annee->estActive() ? ' (Active)' : '' }}
            </option>
            @endforeach
        </select>
    </form>
</div>

@if(!$anneeSelectionnee)
<div class="p-12 text-center bg-white rounded-2xl border border-slate-200">
    <span class="material-symbols-outlined text-slate-300 text-5xl">calendar_today</span>
    <p class="text-sm font-semibold text-slate-400 mt-3">Aucune année académique configurée.</p>
</div>
@else

{{-- KPIs --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-navy-700 uppercase tracking-wider">Total Attendu</p>
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-slate-500 text-lg">account_balance</span>
            </div>
        </div>
        <p class="text-xl font-extrabold text-navy-900">{{ number_format($totalAttendu, 0, ',', ' ') }}</p>
        <p class="text-xs text-navy-700 mt-1">FCFA · {{ $totalEleves }} élèves</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-navy-700 uppercase tracking-wider">Total Encaissé</p>
            <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-lg">payments</span>
            </div>
        </div>
        <p class="text-xl font-extrabold text-emerald-600">{{ number_format($totalEncaisse, 0, ',', ' ') }}</p>
        <p class="text-xs text-navy-700 mt-1">FCFA · {{ $tauxRecouvrement }}% encaissé</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-navy-700 uppercase tracking-wider">Solde Restant</p>
            <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-rose-500 text-lg">money_off</span>
            </div>
        </div>
        <p class="text-xl font-extrabold text-rose-500">{{ number_format($soldeRestant, 0, ',', ' ') }}</p>
        <p class="text-xs text-navy-700 mt-1">FCFA · {{ 100 - $tauxRecouvrement }}% restant</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-bold text-navy-700 uppercase tracking-wider">Taux Recouvrement</p>
            <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-lg">trending_up</span>
            </div>
        </div>
        <p class="text-xl font-extrabold text-navy-900">{{ $tauxRecouvrement }}%</p>
        <div class="mt-2 h-2 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-2 bg-blue-500 rounded-full transition-all" style="width: {{ $tauxRecouvrement }}%"></div>
        </div>
    </div>

</div>

{{-- Répartition + Recouvrement par classe --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    {{-- Répartition statuts --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-navy-900 mb-4">Répartition par statut</h3>
        @php
            $pctEnRetard = $totalEleves > 0 ? round(($nbEnRetard / $totalEleves) * 100, 1) : 0;
            $pctAJour    = $totalEleves > 0 ? round(($nbAJour / $totalEleves) * 100, 1) : 0;
            $pctSolde    = $totalEleves > 0 ? round(($nbSolde / $totalEleves) * 100, 1) : 0;
        @endphp
        <div class="space-y-3">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-navy-700">En retard</span>
                    <span class="text-xs font-bold text-rose-600">{{ $nbEnRetard }} élèves · {{ $pctEnRetard }}%</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-2 bg-rose-400 rounded-full" style="width: {{ $pctEnRetard }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-navy-700">À jour</span>
                    <span class="text-xs font-bold text-amber-600">{{ $nbAJour }} élèves · {{ $pctAJour }}%</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-2 bg-amber-400 rounded-full" style="width: {{ $pctAJour }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold text-navy-700">Soldés</span>
                    <span class="text-xs font-bold text-emerald-600">{{ $nbSolde }} élèves · {{ $pctSolde }}%</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-2 bg-emerald-400 rounded-full" style="width: {{ $pctSolde }}%"></div>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
            <div>
                <p class="text-lg font-extrabold text-rose-500">{{ $nbEnRetard }}</p>
                <p class="text-[10px] font-semibold text-navy-700">En retard</p>
            </div>
            <div>
                <p class="text-lg font-extrabold text-amber-500">{{ $nbAJour }}</p>
                <p class="text-[10px] font-semibold text-navy-700">À jour</p>
            </div>
            <div>
                <p class="text-lg font-extrabold text-emerald-500">{{ $nbSolde }}</p>
                <p class="text-[10px] font-semibold text-navy-700">Soldés</p>
            </div>
        </div>
    </div>

    {{-- Recouvrement par classe --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h3 class="text-sm font-bold text-navy-900 mb-4">Recouvrement par classe</h3>
        @if($classes->isEmpty())
        <p class="text-xs text-slate-400 italic">Aucune classe pour cette année.</p>
        @else
        <div class="space-y-2.5 overflow-y-auto max-h-64">
            @foreach($classes as $row)
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-navy-900 w-16 shrink-0">{{ $row['nom'] }}</span>
                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-2 rounded-full {{ $row['taux'] >= 50 ? 'bg-emerald-400' : ($row['taux'] >= 25 ? 'bg-amber-400' : 'bg-rose-400') }}"
                        style="width: {{ $row['taux'] }}%"></div>
                </div>
                <span class="text-xs font-bold text-navy-700 w-10 text-right shrink-0">{{ $row['taux'] }}%</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Filtres + tableau --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="p-4 border-b border-slate-200">
        <form method="GET" action="{{ route('admin.finances.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="annee_id" value="{{ $anneeSelectionnee->id }}"/>
            <div class="flex-1">
                <select name="classe_id"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">Toutes les classes</option>
                    @foreach($classesFiltre as $classe)
                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                        {{ $classe->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <select name="statut"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">Tous les statuts</option>
                    <option value="en_retard" {{ request('statut') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                    <option value="a_jour"    {{ request('statut') == 'a_jour'    ? 'selected' : '' }}>À jour</option>
                    <option value="solde"     {{ request('statut') == 'solde'     ? 'selected' : '' }}>Soldé</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                Filtrer
            </button>
        </form>
    </div>

    <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-base font-bold text-navy-900">Situation financière des élèves</h2>
        <span class="text-xs text-navy-700 font-medium">{{ $suivis->total() }} élève{{ $suivis->total() > 1 ? 's' : '' }}</span>
    </div>

    @if($suivis->isEmpty())
    <div class="p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">payments</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucun résultat pour ces filtres.</p>
    </div>
    @else

    {{-- Tableau desktop --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left" style="min-width:750px;">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Total Dû</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Total Payé</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Solde Restant</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($suivis as $suivi)
                @php
                    $eleve = $suivi->inscription->eleve;
                    $user  = $eleve->user;
                    $classe = $suivi->inscription->classe;
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-primary">
                                    {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-navy-900">{{ $user->prenom }} {{ $user->nom }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $eleve->numero_matricule }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $classe->nom }}</td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ number_format($suivi->total_du, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-sm font-semibold text-emerald-600">{{ number_format($suivi->total_paye, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 text-sm font-semibold {{ $suivi->solde_restant > 0 ? 'text-rose-500' : 'text-emerald-600' }}">
                        {{ number_format($suivi->solde_restant, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4">
                        @if($suivi->statut === 'solde')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Soldé
                        </span>
                        @elseif($suivi->statut === 'a_jour')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>À jour
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-rose-700 bg-rose-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>En retard
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Vue cartes mobile --}}
    <div class="md:hidden divide-y divide-slate-100">
        @foreach($suivis as $suivi)
        @php
            $eleve  = $suivi->inscription->eleve;
            $user   = $eleve->user;
            $classe = $suivi->inscription->classe;
        @endphp
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-primary">
                            {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-900">{{ $user->prenom }} {{ $user->nom }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $eleve->numero_matricule }} · {{ $classe->nom }}</p>
                    </div>
                </div>
                @if($suivi->statut === 'solde')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Soldé
                </span>
                @elseif($suivi->statut === 'a_jour')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>À jour
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-rose-700 bg-rose-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>En retard
                </span>
                @endif
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-slate-50 rounded-xl p-2">
                    <p class="text-xs font-bold text-navy-900">{{ number_format($suivi->total_du, 0, ',', ' ') }}</p>
                    <p class="text-[10px] text-navy-700">Dû</p>
                </div>
                <div class="bg-emerald-50 rounded-xl p-2">
                    <p class="text-xs font-bold text-emerald-600">{{ number_format($suivi->total_paye, 0, ',', ' ') }}</p>
                    <p class="text-[10px] text-navy-700">Payé</p>
                </div>
                <div class="{{ $suivi->solde_restant > 0 ? 'bg-rose-50' : 'bg-emerald-50' }} rounded-xl p-2">
                    <p class="text-xs font-bold {{ $suivi->solde_restant > 0 ? 'text-rose-500' : 'text-emerald-600' }}">
                        {{ number_format($suivi->solde_restant, 0, ',', ' ') }}
                    </p>
                    <p class="text-[10px] text-navy-700">Restant</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Pagination --}}
    @if($suivis->hasPages())
    <div class="p-4 flex items-center justify-between border-t border-slate-100">
        <p class="text-xs text-navy-700">
            Affichage de <strong>{{ $suivis->firstItem() }}</strong> à <strong>{{ $suivis->lastItem() }}</strong>
            sur <strong>{{ $suivis->total() }}</strong> élèves
        </p>
        <div class="flex items-center gap-1">
            @if($suivis->onFirstPage())
            <span class="px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">←</span>
            @else
            <a href="{{ $suivis->previousPageUrl() }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">←</a>
            @endif

            @foreach($suivis->getUrlRange(max(1, $suivis->currentPage()-2), min($suivis->lastPage(), $suivis->currentPage()+2)) as $page => $url)
            @if($page == $suivis->currentPage())
            <span class="px-3 py-2 text-xs font-bold text-white bg-primary rounded-xl">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">{{ $page }}</a>
            @endif
            @endforeach

            @if($suivis->hasMorePages())
            <a href="{{ $suivis->nextPageUrl() }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">→</a>
            @else
            <span class="px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">→</span>
            @endif
        </div>
    </div>
    @endif

</div>
@endif

@endsection