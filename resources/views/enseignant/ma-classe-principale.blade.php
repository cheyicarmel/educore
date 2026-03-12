@extends('layouts.enseignant')

@section('title', 'Ma Classe Principale — EduCore')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">
                    <span class="material-symbols-outlined text-xs">star</span>Prof Principal
                </span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">{{ $classe->nom }}</h1>
            <p class="text-sm text-navy-700 mt-1">
                {{ $classe->serie->libelle ?? '' }}
                · {{ $effectif }} élève{{ $effectif > 1 ? 's' : '' }}
                · Année <span class="font-semibold">{{ $anneeActive?->libelle }}</span>
            </p>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-2xl p-1.5 w-fit shadow-sm">
        @foreach(['1' => 'Semestre 1', '2' => 'Semestre 2', 'annuel' => 'Annuel'] as $key => $label)
        <a href="?vue={{ $key }}"
            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all
                {{ $vue === $key ? 'bg-primary text-white shadow-sm' : 'text-navy-700 hover:bg-slate-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{--  VUE SEMESTRE 1 & 2  --}}
    @if($vue === '1' || $vue === '2')

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-600 text-xl">checklist</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Saisie des Notes</p>
                <p class="text-2xl font-extrabold text-navy-900">
                    {{ $matieresSaisieComplete }}<span class="text-sm font-semibold text-slate-400">/{{ $totalMatieres }}</span>
                </p>
                <p class="text-xs text-slate-400 mt-0.5">matières complètes</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-600 text-xl">calculate</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Moyennes Calculées</p>
                <p class="text-2xl font-extrabold text-navy-900">
                    {{ $elevesAvecMoyenne }}<span class="text-sm font-semibold text-slate-400">/{{ $effectif }}</span>
                </p>
                <p class="text-xs text-slate-400 mt-0.5">élèves</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-violet-600 text-xl">bar_chart</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Moyenne de Classe</p>
                @if($moyenneClasse !== null)
                <p class="text-2xl font-extrabold text-navy-900">{{ number_format($moyenneClasse, 2) }}<span class="text-sm font-semibold text-slate-400">/20</span></p>
                @else
                <p class="text-sm font-semibold text-slate-400 mt-1">Non calculée</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Statut saisie par matière --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 class="text-base font-bold text-navy-900 mb-4">Statut de Saisie par Matière</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
            @foreach($statutsParMatiere as $statut)
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-navy-900 truncate">{{ $statut['matiere'] }}</p>
                    <p class="text-xs text-navy-700 truncate">{{ $statut['enseignant'] }}</p>
                    <div class="mt-1.5 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-1.5 rounded-full {{ $statut['complet'] ? 'bg-emerald-400' : 'bg-amber-400' }}"
                            style="width: {{ $statut['taux'] }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $statut['notes_saisies'] }}/{{ $statut['notes_attendues'] }} notes</p>
                </div>
                <div class="ml-3 shrink-0">
                    @if($statut['complet'])
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Complet
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Incomplet
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center gap-3">
        @if($toutesNotesSaisies && $elevesAvecMoyenne < $effectif)
        <form method="POST" action="{{ route('enseignant.ma-classe.calculer-moyennes') }}">
            @csrf
            <input type="hidden" name="semestre" value="{{ $vue }}"/>
            <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">calculate</span>
                Calculer les moyennes S{{ $vue }}
            </button>
        </form>
        @elseif($elevesAvecMoyenne === $effectif && $effectif > 0)
        <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-50 text-emerald-700 text-sm font-bold rounded-xl border border-emerald-200">
            <span class="material-symbols-outlined text-base">check_circle</span>
            Moyennes S{{ $vue }} calculées
        </div>
        @else
        <button disabled
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-400 text-sm font-bold rounded-xl cursor-not-allowed">
            <span class="material-symbols-outlined text-base">calculate</span>
            Calculer les moyennes S{{ $vue }}
        </button>
        @endif

        @if($elevesAvecMoyenne === $effectif && $effectif > 0)
        <form method="POST" action="{{ route('enseignant.ma-classe.generer-releve') }}">
            @csrf
            <input type="hidden" name="semestre" value="{{ $vue }}"/>
            <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-700 transition-colors">
                <span class="material-symbols-outlined text-base">description</span>
                Générer le relevé S{{ $vue }}
            </button>
        </form>
        @else
        <button disabled
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-400 text-sm font-bold rounded-xl cursor-not-allowed">
            <span class="material-symbols-outlined text-base">description</span>
            Générer le relevé S{{ $vue }}
        </button>
        @endif
    </div>

    {{-- Tableau élèves semestre --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-navy-900">Élèves — Semestre {{ $vue }}</h2>
            <span class="text-xs text-navy-700 font-medium">Scroll horizontal pour voir toutes les matières →</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width: {{ 400 + (count($statutsParMatiere) * 100) }}px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider sticky left-0 bg-slate-50 z-10" style="min-width:180px;">Élève</th>
                        @if(isset($elevesAvecStats[0]['detail_matieres']))
                            @foreach($elevesAvecStats[0]['detail_matieres'] as $dm)
                            <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center" style="min-width:90px;">
                                {{ \Illuminate\Support\Str::limit($dm['matiere'], 8) }}
                            </th>
                            @endforeach
                        @endif
                        <th class="px-4 py-3 text-[11px] font-bold text-primary uppercase tracking-wider text-center"
                            style="min-width:80px; background: #f0f7ff;">
                            Moy. Gén.
                        </th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center" style="min-width:60px;">Rang</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center" style="min-width:90px;">Mention</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($elevesAvecStats as $item)
                    @php $eleve = $item['eleve']; @endphp
                    <tr class="hover:bg-slate-50 transition-colors" data-inscription="{{ $eleve->id }}">
                        <td class="px-5 py-3 sticky left-0 bg-white z-10">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-navy-900 whitespace-nowrap" data-eleve-nom>{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                            </div>
                        </td>
                        @foreach($item['detail_matieres'] as $dm)
                        <td class="px-3 py-3 text-center">
                            @if($dm['moyenne_generale'] !== null)
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold {{ $dm['moyenne_generale'] >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                                    {{ number_format($dm['moyenne_generale'], 2) }}
                                </span>
                                @if($dm['moyenne_avec_coefficient'] !== null)
                                <span class="text-[10px] text-slate-400">({{ number_format($dm['moyenne_avec_coefficient'], 2) }})</span>
                                @endif
                            </div>
                            @else
                            <span class="text-sm text-slate-300">—</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-4 py-3 text-center" style="background: #f0f7ff;">
                            @if($item['moyenne'] !== null)
                            <span class="inline-flex items-center justify-center w-14 h-7 rounded-lg text-sm font-extrabold
                                {{ $item['moyenne'] >= 10 ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                {{ number_format($item['moyenne'], 2) }}
                            </span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item['rang'])
                            <span class="text-sm font-bold text-navy-900">{{ $item['rang'] }}<sup>{{ $item['rang'] == 1 ? 'er' : 'ème' }}</sup></span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item['mention'])
                            @php
                                $mentionColors = [
                                    'Excellent'   => 'bg-yellow-100 text-yellow-700',
                                    'Très bien'   => 'bg-violet-100 text-violet-700',
                                    'Bien'        => 'bg-emerald-100 text-emerald-700',
                                    'Assez bien'  => 'bg-blue-100 text-blue-700',
                                    'Passable'    => 'bg-amber-100 text-amber-700',
                                    'Insuffisant' => 'bg-rose-100 text-rose-700',
                                ];
                                $color = $mentionColors[$item['mention']] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-flex px-2.5 py-1 {{ $color }} text-[10px] font-bold rounded-full whitespace-nowrap">
                                {{ $item['mention'] }}
                            </span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="99" class="px-5 py-10 text-center text-sm text-slate-400 font-semibold">Aucun élève inscrit.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============ VUE ANNUELLE ============ --}}
    @elseif($vue === 'annuel')

    {{-- KPIs annuels --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-600 text-xl">trending_up</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Élèves Passants</p>
                <p class="text-2xl font-extrabold text-emerald-600">{{ $elevesPassants }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-rose-500 text-xl">replay</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Élèves Doublants</p>
                <p class="text-2xl font-extrabold text-rose-500">{{ $elevesDoublants }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-violet-600 text-xl">bar_chart</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Moyenne Annuelle Classe</p>
                @if($moyenneAnnuelleClasse !== null)
                <p class="text-2xl font-extrabold text-navy-900">{{ number_format($moyenneAnnuelleClasse, 2) }}<span class="text-sm font-semibold text-slate-400">/20</span></p>
                @else
                <p class="text-sm font-semibold text-slate-400 mt-1">Non calculée</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Actions annuelles --}}
    <div class="flex flex-wrap items-center gap-3">
        @if($peutCalculerAnnuel && $elevesAvecMoyenneAnnuelle < $effectif)
        <form method="POST" action="{{ route('enseignant.ma-classe.calculer-moyennes') }}">
            @csrf
            <input type="hidden" name="semestre" value="annuel"/>
            <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">calculate</span>
                Calculer les moyennes annuelles
            </button>
        </form>
        @elseif($elevesAvecMoyenneAnnuelle === $effectif && $effectif > 0)
        <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-50 text-emerald-700 text-sm font-bold rounded-xl border border-emerald-200">
            <span class="material-symbols-outlined text-base">check_circle</span>
            Moyennes annuelles calculées
        </div>
        @else
        <button disabled title="Les moyennes des deux semestres doivent être calculées d'abord"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-400 text-sm font-bold rounded-xl cursor-not-allowed">
            <span class="material-symbols-outlined text-base">calculate</span>
            Calculer les moyennes annuelles
        </button>
        @endif

        @if($elevesAvecMoyenneAnnuelle === $effectif && $effectif > 0)
        <form method="POST" action="{{ route('enseignant.ma-classe.generer-releve') }}">
            @csrf
            <input type="hidden" name="semestre" value="annuel"/>
            <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 text-white text-sm font-bold rounded-xl hover:bg-violet-700 transition-colors">
                <span class="material-symbols-outlined text-base">description</span>
                Générer le relevé annuel
            </button>
        </form>
        @else
        <button disabled
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-400 text-sm font-bold rounded-xl cursor-not-allowed">
            <span class="material-symbols-outlined text-base">description</span>
            Générer le relevé annuel
        </button>
        @endif
    </div>

    {{-- Tableau annuel --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200">
            <h2 class="text-base font-bold text-navy-900">Résultats Annuels</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width: 650px;">
                <thead>
                    {{-- PAS de data-inscription ici --}}
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Moy. S1</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Moy. S2</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Moy. Annuelle</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Rang</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Décision</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($elevesAnnuels as $item)
                    @php $eleve = $item['eleve']; @endphp
                    <tr class="hover:bg-slate-50 transition-colors" data-inscription="{{ $eleve->id }}">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                    </span>
                                </div>
                                {{-- data-eleve-nom ici --}}
                                <p class="text-sm font-semibold text-navy-900" data-eleve-nom>{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold {{ ($item['moy_s1'] ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $item['moy_s1'] !== null ? number_format($item['moy_s1'], 2) : '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-sm font-bold {{ ($item['moy_s2'] ?? 0) >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $item['moy_s2'] !== null ? number_format($item['moy_s2'], 2) : '—' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item['moy_annuelle'] !== null)
                            <span class="text-sm font-extrabold {{ $item['moy_annuelle'] >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ number_format($item['moy_annuelle'], 2) }}
                            </span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item['rang'])
                            <span class="text-sm font-bold text-navy-900">{{ $item['rang'] }}<sup>{{ $item['rang'] == 1 ? 'er' : 'ème' }}</sup></span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($item['decision'])
                            <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full
                                {{ $item['decision'] === 'passant' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ ucfirst($item['decision']) }}
                            </span>
                            @else
                            <span class="text-sm text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400 font-semibold">Aucun élève inscrit.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

</div>

@endsection