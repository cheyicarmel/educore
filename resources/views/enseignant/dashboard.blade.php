@extends('layouts.enseignant')

@section('title', 'Tableau de Bord — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Heading --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Tableau de Bord</h1>
        <p class="text-sm text-navy-700 mt-1">
            Bienvenue, <span class="font-semibold text-navy-900">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</span> — voici vos classes et activités.
        </p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-5">

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">groups</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Mes Classes</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ $totalClasses }}</h3>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">person</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Total Élèves</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ $totalEleves }}</h3>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                </div>
            </div>
            <p class="text-navy-700 text-xs font-medium">Notes Saisies</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ $totalNotes }}</h3>
        </div>

        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                    <span class="material-symbols-outlined text-xl">pending</span>
                </div>
                @if($notesManquantes > 0)
                <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[9px] font-bold rounded uppercase">À faire</span>
                @endif
            </div>
            <p class="text-navy-700 text-xs font-medium">Notes Manquantes</p>
            <h3 class="text-xl md:text-2xl font-bold text-navy-900 mt-0.5">{{ $notesManquantes }}</h3>
        </div>

    </div>

    {{-- Mes classes + Activité récente --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Mes classes --}}
        <div class="lg:col-span-2 bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-navy-900">Mes Classes</h2>
                    <p class="text-xs text-navy-700">Matière : <span class="font-semibold text-navy-900">{{ $matiere }}</span></p>
                </div>
                <a href="{{ route('enseignant.classes.index') }}" class="text-sm font-semibold text-primary hover:underline">Voir tout</a>
            </div>

            @if($attributions->isEmpty())
            <div class="py-8 text-center">
                <span class="material-symbols-outlined text-slate-300 text-4xl">groups</span>
                <p class="text-sm text-slate-400 font-semibold mt-2">Aucune classe assignée pour cette année.</p>
            </div>
            @else
            @php $colors = ['bg-primary/10 text-primary', 'bg-indigo-50 text-indigo-500', 'bg-emerald-50 text-emerald-500', 'bg-violet-50 text-violet-500', 'bg-amber-50 text-amber-500']; @endphp
            <div class="space-y-3">
                @foreach($attributions as $i => $attribution)
                @php
                    $color = $colors[$i % count($colors)];
                    $classe = $attribution->classe;
                    $effectif = $classe->inscriptions()->where('annee_academique_id', $anneeActive?->id)->count();
                @endphp
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg {{ explode(' ', $color)[0] }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined {{ explode(' ', $color)[1] }} text-lg">groups</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold truncate">{{ $classe->nom }}</p>
                            <p class="text-xs text-navy-700">
                                {{ $effectif }} élève{{ $effectif > 1 ? 's' : '' }}
                                @if($attribution->est_prof_principal)
                                · <span class="text-emerald-600 font-semibold">Prof principal</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('enseignant.classes.index') }}"
                        class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors shrink-0">
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Activité récente --}}
        <div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-base md:text-lg font-bold text-navy-900 mb-5">Activité Récente</h2>
            @if($dernieresNotes->isEmpty())
            <div class="py-8 text-center border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-slate-300 text-4xl">history</span>
                <p class="text-sm text-slate-400 font-semibold mt-2">Aucune activité récente.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach($dernieresNotes->take(4) as $note)
                <div class="flex gap-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-emerald-600 text-sm">edit_note</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold">Note saisie</p>
                        <p class="text-xs text-navy-700 truncate">
                            {{ $note->inscription->eleve->user->prenom }} {{ $note->inscription->eleve->user->nom }}
                            · {{ $note->inscription->classe->nom }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5 uppercase">{{ $note->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>

    {{-- Dernières notes saisies --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base md:text-lg font-bold text-navy-900">Dernières Notes Saisies</h2>
            <!-- <a href="{{ route('enseignant.notes.index') }}" class="flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                Saisir des notes
                <span class="material-symbols-outlined text-sm">add</span>
            </a> -->
        </div>

        @if($dernieresNotes->isEmpty())
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-5xl">edit_note</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucune note saisie pour le moment.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width:520px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                        <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                        <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Semestre</th>
                        <th class="px-4 md:px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Note /20</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($dernieresNotes as $note)
                    @php $eleve = $note->inscription->eleve->user; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 md:px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">
                                    {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium whitespace-nowrap">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">{{ $note->inscription->classe->nom }}</td>
                        <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">{{ ucfirst($note->type_note) }}</td>
                        <td class="px-4 md:px-6 py-3 text-sm text-navy-700 whitespace-nowrap">Semestre {{ $note->semestre }}</td>
                        <td class="px-4 md:px-6 py-3 text-right">
                            <span class="text-sm font-bold {{ $note->valeur >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ number_format($note->valeur, 1) }}
                            </span>
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