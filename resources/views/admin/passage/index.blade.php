@extends('layouts.admin')

@section('title', 'Passage de classe — EduCore')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- En-tête --}}
    <div>
        <h1 class="text-2xl font-bold text-navy-900">Passage de classe</h1>
        <p class="text-sm text-navy-700 mt-1">Lancer le passage en classe supérieure pour l'année en cours.</p>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm font-medium">
        <span class="material-symbols-outlined text-base">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm font-medium">
        <span class="material-symbols-outlined text-base">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- État de l'année active --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <h2 class="text-sm font-bold text-navy-900 uppercase tracking-wide">Année en cours</h2>

        @if($anneeActive)
        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">calendar_today</span>
                <div>
                    <p class="text-sm font-bold text-navy-900">{{ $anneeActive->libelle }}</p>
                    <p class="text-xs text-navy-700">Année académique active</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
            </span>
        </div>

        {{-- État des moyennes --}}
        <div class="flex items-center gap-3 p-4 {{ $moyennesPrêtes ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }} rounded-xl">
            <span class="material-symbols-outlined {{ $moyennesPrêtes ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $moyennesPrêtes ? 'check_circle' : 'warning' }}
            </span>
            <div>
                <p class="text-sm font-semibold {{ $moyennesPrêtes ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $moyennesPrêtes ? 'Moyennes annuelles calculées' : 'Moyennes incomplètes' }}
                </p>
                <p class="text-xs {{ $moyennesPrêtes ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $totalMoyennes }} / {{ $totalInscriptions }} élèves ont une moyenne annuelle calculée
                </p>
            </div>
        </div>

        {{-- État des bulletins --}}
        <div class="flex items-center gap-3 p-4 {{ $bulletinsPublies ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }} rounded-xl">
            <span class="material-symbols-outlined {{ $bulletinsPublies ? 'text-emerald-600' : 'text-amber-600' }}">
                {{ $bulletinsPublies ? 'check_circle' : 'warning' }}
            </span>
            <div>
                <p class="text-sm font-semibold {{ $bulletinsPublies ? 'text-emerald-700' : 'text-amber-700' }}">
                    {{ $bulletinsPublies ? 'Tous les bulletins publiés' : 'Bulletins non publiés' }}
                </p>
                <p class="text-xs {{ $bulletinsPublies ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $classesBulletinsOk }} / {{ $totalClasses }} classes ont tous leurs bulletins publiés
                    @if(!$bulletinsPublies)
                    — <a href="{{ route('admin.bulletins.index') }}" class="underline font-bold">Publier les bulletins</a>
                    @endif
                </p>
            </div>
        </div>

        @else
        <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 rounded-xl">
            <span class="material-symbols-outlined text-rose-500">error</span>
            <p class="text-sm font-semibold text-rose-700">Aucune année académique active.</p>
        </div>
        @endif
    </div>

    {{-- Formulaire de passage --}}
    @if($anneeActive && $pret)
    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        <h2 class="text-sm font-bold text-navy-900 uppercase tracking-wide">Lancer le passage</h2>

        @if($anneesSuivantes->isEmpty())
        <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl">
            <span class="material-symbols-outlined text-amber-600">warning</span>
            <div>
                <p class="text-sm font-semibold text-amber-700">Aucune année suivante disponible.</p>
                <p class="text-xs text-amber-600">Créez d'abord une nouvelle année académique dans <a href="{{ route('admin.annees.index') }}" class="underline font-bold">Années Académiques</a>, ainsi que ses classes.</p>
            </div>
        </div>
        @else
        <form method="POST" action="{{ route('admin.passage.traiter') }}"
              onsubmit="return confirm('Êtes-vous sûr de vouloir lancer le passage ? Cette action est irréversible.')">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-navy-700 mb-2 uppercase tracking-wide">Année suivante</label>
                    <select name="annee_suivante_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:outline-none">
                        <option value="">— Sélectionner une année —</option>
                        @foreach($anneesSuivantes as $annee)
                        <option value="{{ $annee->id }}">{{ $annee->libelle }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        Attention — Action irréversible
                    </p>
                    <p>• Les élèves passants seront inscrits automatiquement dans la classe supérieure.</p>
                    <p>• Les élèves doublants seront réinscrits dans la même classe.</p>
                    <p>• Les élèves de Terminale passants seront marqués diplômés.</p>
                    <p>• L'année en cours passera en statut "terminée".</p>
                    <p>• Le solde impayé de chaque élève sera reporté sur la nouvelle année.</p>
                    <p>• Les corrections de série peuvent être faites manuellement après le passage.</p>
                </div>

                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors">
                    <span class="material-symbols-outlined text-base">upgrade</span>
                    Lancer le passage de classe
                </button>
            </div>
        </form>
        @endif
    </div>
    @endif

</div>
@endsection