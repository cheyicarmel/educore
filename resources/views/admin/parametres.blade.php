@extends('layouts.admin')

@section('title', 'Paramètres — EduCore')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Paramètres</h1>
    <p class="text-sm text-navy-700 mt-1">Configurez les informations de votre établissement.</p>
</div>

{{-- Messages --}}
@if(session('success'))
<div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
    <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
</div>
@endif

@if($errors->any())
<div class="mb-5 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3">
    <span class="material-symbols-outlined text-rose-500">error</span>
    <p class="text-sm font-semibold text-rose-700">Veuillez corriger les erreurs ci-dessous.</p>
</div>
@endif

<form method="POST" action="{{ route('admin.parametres.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne gauche — Logo --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-navy-900 mb-4">Logo de l'établissement</h3>
                <div class="flex flex-col items-center gap-4">
                    <div class="w-32 h-32 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden">
                        @if($parametres->logo)
                        <img src="{{ Storage::url($parametres->logo) }}" alt="Logo" class="w-full h-full object-contain p-2"/>
                        @else
                        <span class="material-symbols-outlined text-slate-300 text-5xl">school</span>
                        @endif
                    </div>
                    <div class="w-full">
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Choisir un logo</label>
                        <input type="file" name="logo" accept="image/*"
                            class="w-full text-xs text-navy-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all cursor-pointer"/>
                        <p class="text-xs text-slate-400 mt-1">PNG, JPG ou SVG. Max 2 Mo.</p>
                        @error('logo')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne droite — Informations --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informations générales --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-navy-900 mb-4">Informations générales</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom de l'établissement <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom_etablissement" value="{{ old('nom_etablissement', $parametres->nom_etablissement) }}"
                            placeholder="Ex: Lycée Technique de Cotonou"
                            class="w-full px-4 py-2.5 bg-slate-50 border {{ $errors->has('nom_etablissement') ? 'border-rose-400' : 'border-slate-200' }} rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        @error('nom_etablissement')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Slogan / Devise</label>
                        <input type="text" name="slogan" value="{{ old('slogan', $parametres->slogan) }}"
                            placeholder="Ex: L'excellence au service de l'avenir"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Ville <span class="text-rose-500">*</span></label>
                            <input type="text" name="ville" value="{{ old('ville', $parametres->ville) }}"
                                placeholder="Ex: Cotonou"
                                class="w-full px-4 py-2.5 bg-slate-50 border {{ $errors->has('ville') ? 'border-rose-400' : 'border-slate-200' }} rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                            @error('ville')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Pays <span class="text-rose-500">*</span></label>
                            <input type="text" name="pays" value="{{ old('pays', $parametres->pays) }}"
                                placeholder="Ex: Bénin"
                                class="w-full px-4 py-2.5 bg-slate-50 border {{ $errors->has('pays') ? 'border-rose-400' : 'border-slate-200' }} rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                            @error('pays')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Adresse complète</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $parametres->adresse) }}"
                            placeholder="Ex: Quartier Gbégamey, Rue 12.145"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    </div>
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-navy-900 mb-4">Coordonnées</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Téléphone principal</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $parametres->telephone) }}"
                                placeholder="Ex: +229 21 00 00 01"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Téléphone secondaire</label>
                            <input type="text" name="telephone2" value="{{ old('telephone2', $parametres->telephone2) }}"
                                placeholder="Ex: +229 97 00 00 02"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Email officiel</label>
                        <input type="email" name="email" value="{{ old('email', $parametres->email) }}"
                            placeholder="Ex: contact@lycee.bj"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        @error('email')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Site web</label>
                        <input type="url" name="site_web" value="{{ old('site_web', $parametres->site_web) }}"
                            placeholder="Ex: https://monlycee.bj"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        @error('site_web')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Configuration bulletins --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-navy-900 mb-1">Configuration des bulletins</h3>
                <p class="text-xs text-slate-400 mb-4">Ces seuils seront utilisés pour afficher les mentions sur les bulletins.</p>
                <div class="space-y-3">
                    @foreach([
                        ['key' => 'seuil_insuffisant', 'label' => 'Insuffisant', 'color' => 'rose',   'prefix' => 'En dessous de'],
                        ['key' => 'seuil_passable',    'label' => 'Passable',    'color' => 'amber',  'prefix' => 'À partir de'],
                        ['key' => 'seuil_assez_bien',  'label' => 'Assez bien',  'color' => 'blue',   'prefix' => 'À partir de'],
                        ['key' => 'seuil_bien',        'label' => 'Bien',        'color' => 'emerald','prefix' => 'À partir de'],
                        ['key' => 'seuil_tres_bien',   'label' => 'Très bien',   'color' => 'violet', 'prefix' => 'À partir de'],
                        ['key' => 'seuil_excellent',   'label' => 'Excellent',   'color' => 'yellow', 'prefix' => 'À partir de'],
                    ] as $seuil)
                    <div class="grid grid-cols-3 gap-3 items-center">
                        <div class="col-span-1">
                            <span class="inline-flex items-center px-2.5 py-1 bg-{{ $seuil['color'] }}-50 text-{{ $seuil['color'] }}-700 text-xs font-bold rounded-lg">
                                {{ $seuil['label'] }}
                            </span>
                        </div>
                        <div class="col-span-2 flex items-center gap-2">
                            <span class="text-xs text-navy-700 font-semibold shrink-0">{{ $seuil['prefix'] }}</span>
                            <input type="number" name="{{ $seuil['key'] }}"
                                value="{{ old($seuil['key'], $parametres->{$seuil['key']}) }}"
                                min="0" max="20" step="0.5"
                                class="w-20 px-3 py-2 bg-slate-50 border {{ $errors->has($seuil['key']) ? 'border-rose-400' : 'border-slate-200' }} rounded-xl text-sm text-center font-bold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                            <span class="text-xs text-navy-700">/20</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bouton save --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-base">save</span>
                    Enregistrer les paramètres
                </button>
            </div>

        </div>
    </div>

</form>

@endsection