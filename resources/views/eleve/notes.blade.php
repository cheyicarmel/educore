@extends('layouts.eleve')

@section('title', 'Mes Notes — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mes Notes</h1>
            <p class="text-sm text-navy-700 mt-1">
                <span class="font-semibold text-navy-900">{{ $classe?->nom ?? '—' }}</span>
                · Année <span class="font-semibold">{{ $anneeActive?->libelle }}</span>
            </p>
        </div>
        <form method="GET" action="{{ route('eleve.notes') }}">
            <select name="semestre" onchange="this.form.submit()"
                class="px-7 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="1" {{ $semestre == 1 ? 'selected' : '' }}>Semestre 1</option>
                <option value="2" {{ $semestre == 2 ? 'selected' : '' }}>Semestre 2</option>
            </select>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">calculate</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Moyenne Générale S{{ $semestre }}</p>
            @if($moyenneGenerale !== null)
            <h3 class="text-xl font-bold text-navy-900 mt-0.5">
                {{ number_format($moyenneGenerale, 2) }}<span class="text-sm font-medium text-navy-700">/20</span>
            </h3>
            @else
            <p class="text-sm font-semibold text-slate-400 mt-1">Non calculée</p>
            @endif
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">checklist</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Matières Complètes</p>
            <h3 class="text-xl font-bold text-navy-900 mt-0.5">
                {{ $matieresCompletes }}<span class="text-sm font-medium text-navy-700">/{{ $totalMatieres }}</span>
            </h3>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">grade</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Notes Reçues</p>
            <h3 class="text-xl font-bold text-navy-900 mt-0.5">
                {{ $notesRecues }}<span class="text-sm font-medium text-navy-700">/{{ $notesAttendues }}</span>
            </h3>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">pending</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Notes Manquantes</p>
            <h3 class="text-xl font-bold text-navy-900 mt-0.5">
                {{ $notesAttendues - $notesRecues }}<span class="text-sm font-medium text-navy-700"> notes</span>
            </h3>
        </div>
    </div>

    {{-- Tableau par matière --}}
    @if($matieresAvecNotes->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">grade</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucune matière assignée pour cette classe.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($matieresAvecNotes as $item)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            {{-- En-tête matière --}}
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-extrabold text-navy-900">{{ $item['matiere'] }}</p>
                    <p class="text-xs text-navy-700 mt-0.5">Prof : {{ $item['enseignant'] }}</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if($item['moy_gen'] !== null)
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Moy. Générale</p>
                        <p class="text-lg font-extrabold {{ $item['moy_gen'] >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ number_format($item['moy_gen'], 2) }}<span class="text-xs font-semibold text-slate-400">/20</span>
                        </p>
                    </div>
                    @endif
                    @if($item['complet'])
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Complet
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Incomplet
                    </span>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="min-width:600px;">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 1</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 2</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 3</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-indigo-500 uppercase tracking-wider text-center bg-indigo-50/50">Moy. Interro</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Devoir 1</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Devoir 2</th>
                            <th class="px-5 py-3 text-[11px] font-bold text-emerald-600 uppercase tracking-wider text-center bg-emerald-50/50">Moy. Générale</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach(['interrogation1','interrogation2','interrogation3'] as $type)
                            <td class="px-5 py-4 text-center">
                                @if($item['notes'][$type] !== null)
                                <span class="inline-flex items-center justify-center w-12 h-10 rounded-xl text-sm font-extrabold
                                    {{ $item['notes'][$type] >= 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                    {{ number_format($item['notes'][$type], 1) }}
                                </span>
                                @else
                                <span class="inline-flex items-center justify-center w-12 h-10 rounded-xl text-sm font-bold bg-slate-50 text-slate-400 border border-dashed border-slate-300">
                                    —
                                </span>
                                @endif
                            </td>
                            @endforeach

                            {{-- Moy. Interro --}}
                            <td class="px-5 py-4 text-center bg-indigo-50/30">
                                @if($item['moy_interro'] !== null)
                                <span class="inline-flex items-center justify-center w-14 h-10 rounded-xl text-sm font-extrabold
                                    {{ $item['moy_interro'] >= 10 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                    {{ number_format($item['moy_interro'], 2) }}
                                </span>
                                @else
                                <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>

                            @foreach(['devoir1','devoir2'] as $type)
                            <td class="px-5 py-4 text-center">
                                @if($item['notes'][$type] !== null)
                                <span class="inline-flex items-center justify-center w-12 h-10 rounded-xl text-sm font-extrabold
                                    {{ $item['notes'][$type] >= 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                    {{ number_format($item['notes'][$type], 1) }}
                                </span>
                                @else
                                <span class="inline-flex items-center justify-center w-12 h-10 rounded-xl text-sm font-bold bg-slate-50 text-slate-400 border border-dashed border-slate-300">
                                    —
                                </span>
                                @endif
                            </td>
                            @endforeach

                            {{-- Moy. Générale --}}
                            <td class="px-5 py-4 text-center bg-emerald-50/30">
                                @if($item['moy_gen'] !== null)
                                <span class="inline-flex items-center justify-center w-14 h-10 rounded-xl text-sm font-extrabold
                                    {{ $item['moy_gen'] >= 10 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                    {{ number_format($item['moy_gen'], 2) }}
                                </span>
                                @else
                                <span class="text-sm text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection