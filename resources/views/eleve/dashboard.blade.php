@extends('layouts.eleve')

@section('title', 'Mon Espace — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mon Espace</h1>
            <p class="text-sm text-navy-700 mt-1">
                Bienvenue, {{ $user->prenom }} — voici un résumé de ta scolarité.
            </p>
        </div>
        <div class="shrink-0 flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-xs font-bold rounded-full">
                <span class="material-symbols-outlined text-sm">groups</span>
                {{ $classe?->nom ?? '—' }}
            </span>
            <form method="GET" action="{{ route('eleve.dashboard') }}">
                <select name="vue" onchange="this.form.submit()"
                    class="px-7 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="1"      {{ $vue === '1'      ? 'selected' : '' }}>Semestre 1</option>
                    <option value="2"      {{ $vue === '2'      ? 'selected' : '' }}>Semestre 2</option>
                    <option value="annuel" {{ $vue === 'annuel' ? 'selected' : '' }}>Annuel</option>
                </select>
            </form>
        </div>
    </div>

    

    {{-- ══ VUE SEMESTRE ══ --}}
    @if($vue === '1' || $vue === '2')

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">calculate</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Moyenne Générale S{{ $semestreActif }}</p>
            @if($moyenneGenerale !== null)
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">
                {{ number_format($moyenneGenerale, 1) }}<span class="text-sm font-medium text-navy-700">/20</span>
            </h3>
            @else
            <p class="text-sm font-semibold text-slate-400 mt-1">Non calculée</p>
            @endif
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">leaderboard</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Classement S{{ $semestreActif }}</p>
            @if($rang !== null)
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">
                {{ $rang }}<span class="text-sm font-medium text-navy-700">{{ $rang == 1 ? 'er' : 'ème' }} / {{ $effectif }}</span>
            </h3>
            @else
            <p class="text-sm font-semibold text-slate-400 mt-1">Non classé</p>
            @endif
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">grade</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Notes Reçues S{{ $semestreActif }}</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">
                {{ $notesRecues }}<span class="text-sm font-medium text-navy-700">/{{ $notesAttendues }}</span>
            </h3>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg w-fit">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
                @if($statutFinancier === 'en_retard')
                <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-bold rounded uppercase">Retard</span>
                @elseif($statutFinancier === 'partiel')
                <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-[9px] font-bold rounded uppercase">Partiel</span>
                @else
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-[9px] font-bold rounded uppercase">À jour</span>
                @endif
            </div>
            <p class="text-navy-700 text-xs font-medium">Solde Restant</p>
            @if($soldeRestant > 0)
            <h3 class="text-lg md:text-xl font-bold text-navy-900 mt-0.5">
                {{ number_format($soldeRestant, 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span>
            </h3>
            @else
            <h3 class="text-lg md:text-xl font-bold text-emerald-600 mt-0.5">Soldé</h3>
            @endif
        </div>

    </div>

    {{-- Notes par matière + Suivi financier --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Notes — Semestre {{ $semestreActif }}</h2>
                    <p class="text-xs text-navy-700">Moyennes générales par matière</p>
                </div>
                <a href="{{ route('eleve.notes') }}" class="text-sm font-semibold text-primary hover:underline">Détail</a>
            </div>
            @if($moyennesParMatiere->isEmpty())
            <div class="py-8 text-center">
                <span class="material-symbols-outlined text-slate-300 text-4xl">grade</span>
                <p class="text-sm font-semibold text-slate-400 mt-2">Aucune moyenne disponible pour ce semestre.</p>
            </div>
            @else
            @php $barColors = ['bg-emerald-500','bg-blue-500','bg-indigo-500','bg-violet-500','bg-amber-500','bg-rose-400','bg-teal-500']; @endphp
            <div class="space-y-3">
                @foreach($moyennesParMatiere as $i => $item)
                @php $pct = min(100, round(($item['moyenne'] / 20) * 100)); $color = $barColors[$i % count($barColors)]; @endphp
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-navy-700 w-32 shrink-0 truncate">{{ $item['matiere'] }}</span>
                    <div class="flex-1 bg-slate-100 rounded-full h-2">
                        <div class="{{ $color }} h-2 rounded-full transition-all" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="text-xs font-bold {{ $item['moyenne'] >= 10 ? 'text-navy-900' : 'text-rose-500' }} w-10 text-right shrink-0">
                        {{ number_format($item['moyenne'], 1) }}
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Suivi financier --}}
        @include('eleve.partials.suivi-financier')
    </div>

    {{-- ══ VUE ANNUELLE ══ --}}
    @elseif($vue === 'annuel')

    {{-- KPIs annuels --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">calculate</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Moyenne Annuelle</p>
            @if($moyenneAnnuelle !== null)
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">
                {{ number_format($moyenneAnnuelle, 1) }}<span class="text-sm font-medium text-navy-700">/20</span>
            </h3>
            @else
            <p class="text-sm font-semibold text-slate-400 mt-1">Non calculée</p>
            @endif
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">leaderboard</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Classement Annuel</p>
            @if($rangAnnuel !== null)
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">
                {{ $rangAnnuel }}<span class="text-sm font-medium text-navy-700">{{ $rangAnnuel == 1 ? 'er' : 'ème' }} / {{ $effectif }}</span>
            </h3>
            @else
            <p class="text-sm font-semibold text-slate-400 mt-1">Non classé</p>
            @endif
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">trending_up</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Décision</p>
            @if($decisionAnnuelle)
            <h3 class="text-xl md:text-2xl font-bold mt-0.5 {{ $decisionAnnuelle === 'passant' ? 'text-emerald-600' : 'text-rose-500' }}">
                {{ ucfirst($decisionAnnuelle) }}
            </h3>
            @else
            <p class="text-sm font-semibold text-slate-400 mt-1">Non disponible</p>
            @endif
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg w-fit">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
                @if($statutFinancier === 'en_retard')
                <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-bold rounded uppercase">Retard</span>
                @elseif($statutFinancier === 'partiel')
                <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-[9px] font-bold rounded uppercase">Partiel</span>
                @else
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-[9px] font-bold rounded uppercase">À jour</span>
                @endif
            </div>
            <p class="text-navy-700 text-xs font-medium">Solde Restant</p>
            @if($soldeRestant > 0)
            <h3 class="text-lg md:text-xl font-bold text-navy-900 mt-0.5">
                {{ number_format($soldeRestant, 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span>
            </h3>
            @else
            <h3 class="text-lg md:text-xl font-bold text-emerald-600 mt-0.5">Soldé</h3>
            @endif
        </div>

    </div>

    {{-- Moyennes annuelles par matière + Suivi financier --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Moyennes Annuelles par Matière</h2>
                    <p class="text-xs text-navy-700">Moyenne de S1 et S2 par matière</p>
                </div>
            </div>
            @if($moyennesAnnuellesParMatiere->isEmpty())
            <div class="py-8 text-center">
                <span class="material-symbols-outlined text-slate-300 text-4xl">grade</span>
                <p class="text-sm font-semibold text-slate-400 mt-2">Aucune moyenne annuelle disponible.</p>
            </div>
            @else
            @php $barColors = ['bg-emerald-500','bg-blue-500','bg-indigo-500','bg-violet-500','bg-amber-500','bg-rose-400','bg-teal-500']; @endphp
            <div class="space-y-4">
                @foreach($moyennesAnnuellesParMatiere as $i => $item)
                @php
                    $pct   = $item['moyenne'] ? min(100, round(($item['moyenne'] / 20) * 100)) : 0;
                    $color = $barColors[$i % count($barColors)];
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-navy-700 truncate">{{ $item['matiere'] }}</span>
                        <span class="text-xs font-bold {{ ($item['moyenne'] ?? 0) >= 10 ? 'text-navy-900' : 'text-rose-500' }}">
                            {{ $item['moyenne'] !== null ? number_format($item['moyenne'], 1) : '—' }}
                        </span>
                    </div>
                    <div class="flex-1 bg-slate-100 rounded-full h-2">
                        <div class="{{ $color }} h-2 rounded-full transition-all" style="width:{{ $pct }}%"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-[10px] text-slate-400">S1 : {{ $item['moy_s1'] !== null ? number_format($item['moy_s1'], 1) : '—' }}</span>
                        <span class="text-[10px] text-slate-400">S2 : {{ $item['moy_s2'] !== null ? number_format($item['moy_s2'], 1) : '—' }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Suivi financier --}}
        @include('eleve.partials.suivi-financier')
    </div>

    @endif

    {{-- Bulletins --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Bulletins</h2>
            <a href="{{ route('eleve.bulletins') }}" class="text-sm font-semibold text-primary hover:underline">Voir tout</a>
        </div>
        @if($bulletins->isEmpty())
        <div class="p-10 text-center">
            <span class="material-symbols-outlined text-slate-300 text-4xl">description</span>
            <p class="text-sm font-semibold text-slate-400 mt-2">Aucun bulletin disponible pour le moment.</p>
        </div>
        @else
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
                    @foreach($bulletins as $bulletin)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 md:px-6 py-3 text-sm font-medium whitespace-nowrap">{{ $bulletin['periode'] }}</td>
                        <td class="px-4 md:px-6 py-3">
                            <span class="text-sm font-bold {{ $bulletin['moyenne'] >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ number_format($bulletin['moyenne'], 2) }} / 20
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">
                            {{ $bulletin['rang'] }}{{ $bulletin['rang'] == 1 ? 'er' : 'ème' }} / {{ $bulletin['effectif'] }}
                        </td>
                        <td class="px-4 md:px-6 py-3 text-right">
                            <a href="{{ route('eleve.bulletins.download', $bulletin['id']) }}"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline whitespace-nowrap">
                                <span class="material-symbols-outlined text-sm">download</span>Télécharger
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection