@extends('layouts.comptable')

@section('title', 'Documents Financiers — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Documents Financiers</h1>
        <p class="text-sm text-navy-700 mt-1">Générez vos documents officiels pour l'année <span class="font-semibold">{{ $anneeActive?->libelle }}</span>.</p>
    </div>

    {{-- Documents disponibles --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        {{-- Rapport Financier Global --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col gap-4">
            <div class="flex items-start gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                    <span class="material-symbols-outlined text-2xl">assessment</span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-navy-900">Rapport Financier Global</h2>
                    <p class="text-xs text-navy-700 mt-1">Récapitulatif complet — total dû, encaissé, soldes restants, taux de recouvrement et répartition par classe.</p>
                </div>
            </div>
            <a href="{{ route('comptable.documents.rapport-global') }}" target="_blank"
                class="mt-auto inline-flex items-center justify-center gap-2 w-full py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                Générer le PDF
            </a>
        </div>

    </div>
</div>
@endsection