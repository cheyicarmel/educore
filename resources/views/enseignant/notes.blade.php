@extends('layouts.enseignant')

@section('title', 'Saisie des Notes — EduCore')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('enseignant.classes.index') }}" class="text-navy-700 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Saisie des Notes</h1>
            </div>
            <p class="text-sm text-navy-700">
                <span class="font-semibold text-navy-900">{{ $classe->nom }}</span>
                · {{ $matiere->nom }}
                · <span class="font-semibold">Semestre {{ $semestre }}</span>
            </p>
        </div>
        <form method="GET" action="{{ route('enseignant.notes.index') }}" class="flex items-center gap-2 shrink-0">
            <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
            <select name="semestre" onchange="this.form.submit()"
                class="px-9 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="1" {{ $semestre == 1 ? 'selected' : '' }}>Semestre 1</option>
                <option value="2" {{ $semestre == 2 ? 'selected' : '' }}>Semestre 2</option>
            </select>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-600 text-xl">bar_chart</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Moyenne de Classe</p>
                @if($moyenneClasse !== null)
                <p class="text-2xl font-extrabold text-navy-900">{{ number_format($moyenneClasse, 1) }}<span class="text-sm font-semibold text-slate-400">/20</span></p>
                @else
                <p class="text-sm font-semibold text-slate-400 mt-1">Pas encore calculée</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-amber-500 text-xl">task_alt</span>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Taux de Saisie</p>
                <p class="text-2xl font-extrabold text-navy-900">{{ $tauxSaisie }}<span class="text-sm font-semibold text-slate-400">%</span>
                    <span class="text-xs font-semibold text-slate-400 ml-1">{{ $notesSaisies }}/{{ $notesAttendues }}</span>
                </p>
                <div class="mt-1.5 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-1.5 bg-amber-400 rounded-full" style="width: {{ $tauxSaisie }}%"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-rose-500 text-xl">group</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Élèves Incomplets</p>
                <p class="text-2xl font-extrabold text-navy-900">{{ $elevesIncomplets }}
                    <span class="text-sm font-semibold text-slate-400">à compléter</span>
                </p>
            </div>
        </div>

    </div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-rose-500">error</span>
        <p class="text-sm font-semibold text-rose-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-navy-900">Notes — Semestre {{ $semestre }}</h2>
            <span class="text-xs text-navy-700 font-medium">{{ $inscriptions->count() }} élève{{ $inscriptions->count() > 1 ? 's' : '' }}</span>
        </div>

        @if($inscriptions->isEmpty())
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-5xl">group</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucun élève inscrit dans cette classe.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width: 700px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider w-48">Nom de l'élève</th>
                        <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 1</th>
                        <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 2</th>
                        <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 3</th>
                        <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Devoir 1</th>
                        <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Devoir 2</th>
                        <th class="px-3 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($inscriptions as $inscription)
                    @php
                        $eleve  = $inscription->eleve->user;
                        $notes  = $notesParInscription[$inscription->id] ?? [];
                        $types  = ['interrogation1', 'interrogation2', 'interrogation3', 'devoir1', 'devoir2'];
                        $complet = count(array_filter($types, fn($t) => isset($notes[$t]))) === 5;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">

                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-navy-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                            </div>
                        </td>

                        @foreach($types as $type)
                            @php $valeur = $notes[$type] ?? null; @endphp
                            <td class="px-3 py-3 text-center">
                                <form method="POST" action="{{ route('enseignant.notes.store') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="inscription_id" value="{{ $inscription->id }}"/>
                                    <input type="hidden" name="matiere_id" value="{{ $matiere->id }}"/>
                                    <input type="hidden" name="numero_semestre" value="{{ $semestre }}"/>
                                    <input type="hidden" name="type" value="{{ $type }}"/>
                                    <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                    <input
                                        type="number"
                                        name="valeur"
                                        value="{{ $valeur !== null ? $valeur + 0 : '' }}"
                                        min="0" max="20" step="0.5"
                                        placeholder="--"
                                        onchange="this.form.submit()"
                                        class="w-14 px-2 py-1.5 text-sm font-bold text-center rounded-xl border
                                            {{ $valeur !== null ? 'bg-slate-50 border-slate-200 text-navy-900' : 'bg-white border-dashed border-slate-300 text-slate-400' }}
                                            focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    />
                                </form>
                            </td>
                        @endforeach

                        <td class="px-3 py-3 text-center">
                            @if($complet)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Complet
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Incomplet
                            </span>
                            @endif
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