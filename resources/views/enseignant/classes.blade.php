@extends('layouts.enseignant')

@section('title', 'Mes Classes — EduCore')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mes Classes</h1>
        <p class="text-sm text-navy-700 mt-1">
            Matière enseignée : <span class="font-semibold text-navy-900">{{ $matiere }}</span>
            · Année <span class="font-semibold text-navy-900">{{ $anneeActive?->libelle ?? '—' }}</span>
        </p>
    </div>

    @if($attributions->isEmpty())
    <div class="p-12 text-center bg-white rounded-2xl border border-slate-200">
        <span class="material-symbols-outlined text-slate-300 text-5xl">groups</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucune classe assignée pour cette année.</p>
    </div>
    @else

    {{-- Grille des classes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @php $colors = [
            ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'border' => 'border-primary/20'],
            ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-500', 'border' => 'border-indigo-200'],
            ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'border' => 'border-emerald-200'],
            ['bg' => 'bg-violet-50',  'text' => 'text-violet-500', 'border' => 'border-violet-200'],
            ['bg' => 'bg-amber-50',   'text' => 'text-amber-500',  'border' => 'border-amber-200'],
        ]; @endphp

        @foreach($attributions as $i => $attribution)
        @php
            $color   = $colors[$i % count($colors)];
            $classe  = $attribution->classe;
            $stats   = $statsParClasse[$classe->id] ?? ['effectif' => 0, 'notes_saisies' => 0, 'notes_attendues' => 0, 'complet' => false];
        @endphp

        {{-- data-classe-card sur chaque carte --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow" data-classe-card>

            {{-- En-tête carte --}}
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl {{ $color['bg'] }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined {{ $color['text'] }} text-xl">groups</span>
                        </div>
                        <div>
                            {{-- data-classe-nom sur le nom de la classe --}}
                            <p class="text-base font-extrabold text-navy-900" data-classe-nom>{{ $classe->nom }}</p>
                            <p class="text-xs text-navy-700">{{ $classe->serie->libelle ?? '' }} · {{ ucfirst($classe->cycle) }} cycle</p>
                        </div>
                    </div>
                    @if($attribution->est_prof_principal)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">
                        <span class="material-symbols-outlined text-xs">star</span>Prof Principal
                    </span>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-navy-900">{{ $stats['effectif'] }}</p>
                        <p class="text-[10px] font-semibold text-navy-700 mt-0.5">Élèves</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-navy-900">{{ $stats['notes_saisies'] }}</p>
                        <p class="text-[10px] font-semibold text-navy-700 mt-0.5">Notes saisies</p>
                    </div>
                    <div class="{{ $stats['complet'] ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-xl p-3">
                        <p class="text-lg font-extrabold {{ $stats['complet'] ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $stats['notes_attendues'] - $stats['notes_saisies'] }}
                        </p>
                        <p class="text-[10px] font-semibold text-navy-700 mt-0.5">Manquantes</p>
                    </div>
                </div>

                {{-- Barre progression --}}
                @php
                    $pct = $stats['notes_attendues'] > 0
                        ? min(100, round(($stats['notes_saisies'] / $stats['notes_attendues']) * 100))
                        : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold text-navy-700">Progression saisie</span>
                        <span class="text-xs font-bold {{ $pct == 100 ? 'text-emerald-600' : 'text-navy-900' }}">{{ $pct }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-2 rounded-full transition-all {{ $pct == 100 ? 'bg-emerald-400' : 'bg-primary' }}"
                            style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="pt-1">
                    <a href="{{ route('enseignant.notes.index', ['classe_id' => $classe->id]) }}"
                        class="w-full flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">
                        <span class="material-symbols-outlined text-sm">edit_note</span>
                        Saisir les notes
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @endif

</div>

@endsection