@extends('layouts.eleve')

@section('title', 'Mon Profil — EduCore')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mon Profil</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez vos informations personnelles et votre mot de passe</p>
    </div>

    {{-- Notifications --}}
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
    @if($errors->any())
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
        <div class="flex items-center gap-3 mb-2">
            <span class="material-symbols-outlined text-rose-500">error</span>
            <p class="text-sm font-semibold text-rose-700">Veuillez corriger les erreurs suivantes :</p>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
            <li class="text-sm text-rose-600">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne gauche --}}
        <div class="space-y-5">

            {{-- Carte identité --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col items-center text-center gap-3">
                <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-2xl font-extrabold text-primary">
                        {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <p class="text-base font-extrabold text-navy-900">{{ $user->prenom }} {{ $user->nom }}</p>
                    <p class="text-sm text-navy-700">{{ $user->email }}</p>
                    <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Élève
                    </span>
                </div>
            </div>

            {{-- Infos scolarité --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="text-sm font-bold text-navy-900 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">school</span>
                    Ma Scolarité — {{ $anneeActive?->libelle }}
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-navy-700">Classe</span>
                        <span class="text-xs font-bold text-navy-900">{{ $classe?->nom ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-navy-700">Série</span>
                        <span class="text-xs font-bold text-navy-900">{{ $classe?->serie->libelle ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-navy-700">Matricule</span>
                        <span class="text-xs font-bold text-navy-900">{{ $matricule }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Colonne droite --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Informations personnelles --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-base font-bold text-navy-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">person</span>
                        Informations personnelles
                    </h2>
                </div>
                <form method="POST" action="{{ route('eleve.profil.update') }}" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="infos"/>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                required/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom', $user->nom) }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                required/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Adresse email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            required/>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-base">save</span>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            {{-- Mot de passe --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h2 class="text-base font-bold text-navy-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-base">lock</span>
                        Changer le mot de passe
                    </h2>
                </div>
                <form method="POST" action="{{ route('eleve.profil.update') }}" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="password"/>
                    <div>
                        <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Mot de passe actuel</label>
                        <input type="password" name="current_password"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                            required/>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Nouveau mot de passe</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    class="w-full px-4 py-2.5 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    required/>
                                <button type="button" onclick="toggleVisibilite('password', 'eye1')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-navy-700 transition-colors">
                                    <span id="eye1" class="material-symbols-outlined text-lg">visibility</span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Confirmer le mot de passe</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full px-4 py-2.5 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    required/>
                                <button type="button" onclick="toggleVisibilite('password_confirmation', 'eye2')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-navy-700 transition-colors">
                                    <span id="eye2" class="material-symbols-outlined text-lg">visibility</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-base">lock_reset</span>
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleVisibilite(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type = 'text';
        eye.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        eye.textContent = 'visibility';
    }
}
</script>
@endsection