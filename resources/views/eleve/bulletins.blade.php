@extends('layouts.eleve')

@section('title', 'Mes Bulletins — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mes Bulletins</h1>
        <p class="text-sm text-navy-700 mt-1">
            <span class="font-semibold text-navy-900">{{ $classe?->nom ?? '—' }}</span>
            · Année <span class="font-semibold">{{ $anneeActive?->libelle }}</span>
        </p>
    </div>

    {{-- 3 cartes bulletins --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($bulletins->sortBy('id') as $bulletin)
        @php
            $isAnnuel = $bulletin['type'] === 'annuel';
            $dispo    = $bulletin['disponible'];
        @endphp
        <div class="bg-white rounded-2xl border {{ $dispo ? 'border-slate-200' : 'border-dashed border-slate-300' }} shadow-sm overflow-hidden">

            {{-- En-tête --}}
            <div class="p-5 border-b {{ $dispo ? 'border-slate-100' : 'border-dashed border-slate-200' }} flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                        {{ $isAnnuel ? 'bg-violet-50' : 'bg-primary/10' }}">
                        <span class="material-symbols-outlined {{ $isAnnuel ? 'text-violet-600' : 'text-primary' }} text-xl">
                            {{ $isAnnuel ? 'workspace_premium' : 'description' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-extrabold text-navy-900">{{ $bulletin['periode'] }}</p>
                        <p class="text-xs text-navy-700">{{ $bulletin['annee'] }}</p>
                    </div>
                </div>
                @if($dispo)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Disponible
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>En attente
                </span>
                @endif
            </div>

            {{-- Contenu --}}
            <div class="p-5 space-y-3">
                @if($dispo)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-navy-700">Moyenne</span>
                    <span class="text-sm font-extrabold {{ $bulletin['moyenne'] >= 10 ? 'text-emerald-600' : 'text-rose-500' }}">
                        {{ number_format($bulletin['moyenne'], 2) }} / 20
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-navy-700">Rang</span>
                    <span class="text-sm font-bold text-navy-900">
                        {{ $bulletin['rang'] }}{{ $bulletin['rang'] == 1 ? 'er' : 'ème' }} / {{ $bulletin['effectif'] }}
                    </span>
                </div>
                @if($isAnnuel && isset($bulletin['decision']))
                <div class="flex items-center justify-between">
                    <span class="text-xs text-navy-700">Décision</span>
                    <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-full
                        {{ $bulletin['decision'] === 'passant' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ ucfirst($bulletin['decision']) }}
                    </span>
                </div>
                @endif

                {{-- Barre moyenne --}}
                <div class="pt-1">
                    @php $pct = min(100, round(($bulletin['moyenne'] / 20) * 100)); @endphp
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $bulletin['moyenne'] >= 10 ? 'bg-emerald-400' : 'bg-rose-400' }}"
                            style="width:{{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Bouton télécharger --}}
                <div class="pt-2">
                    <a href="{{ route('eleve.bulletins.download', $bulletin['id']) }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-base">download</span>
                        Télécharger le bulletin
                    </a>
                </div>

                @else
                <div class="py-4 text-center">
                    <span class="material-symbols-outlined text-slate-300 text-4xl">hourglass_empty</span>
                    <p class="text-xs font-semibold text-slate-400 mt-2">
                        Bulletin pas encore disponible.<br>Revenez plus tard.
                    </p>
                </div>
                @endif
            </div>

        </div>
        @endforeach
    </div>

</div>
@endsection