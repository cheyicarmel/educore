@extends('layouts.comptable')

@section('title', 'Mon Profil — EduCore')

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Mon Profil</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez vos informations personnelles et votre mot de passe.</p>
    </div>

    {{-- Carte identité --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 md:p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xl shrink-0">
                {{ strtoupper(substr($user->prenom, 0, 1)) }}{{ strtoupper(substr($user->nom, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-navy-900">{{ $user->prenom }} {{ $user->nom }}</h2>
                <p class="text-sm text-navy-700">{{ $user->email }}</p>
                <span class="inline-flex mt-1 px-2.5 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Comptable</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    @php $tab = session('tab', 'infos'); @endphp
    <div x-data="{ tab: '{{ $tab }}' }">

        {{-- Tab buttons --}}
        <div class="flex gap-2 border-b border-slate-200 mb-6">
            <button @click="tab = 'infos'"
                :class="tab === 'infos' ? 'border-b-2 border-primary text-primary font-bold' : 'text-navy-700 hover:text-navy-900'"
                class="px-4 py-2.5 text-sm font-semibold transition-colors -mb-px">
                Informations
            </button>
            <button @click="tab = 'password'"
                :class="tab === 'password' ? 'border-b-2 border-primary text-primary font-bold' : 'text-navy-700 hover:text-navy-900'"
                class="px-4 py-2.5 text-sm font-semibold transition-colors -mb-px">
                Mot de passe
            </button>
        </div>

        {{-- Tab Informations --}}
        <div x-show="tab === 'infos'">
            @if(session('success_infos'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl mb-5 text-sm font-semibold">
                <span class="material-symbols-outlined text-base">check_circle</span>
                {{ session('success_infos') }}
            </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 md:p-6">
                <form method="POST" action="{{ route('comptable.profil.infos') }}">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Prénom</label>
                            <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('prenom') border-rose-400 @enderror"/>
                            @error('prenom')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Nom</label>
                            <input type="text" name="nom" value="{{ old('nom', $user->nom) }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('nom') border-rose-400 @enderror"/>
                            @error('nom')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold text-slate-400 cursor-not-allowed"/>
                            <p class="text-[10px] text-slate-400 mt-1">L'email ne peut pas être modifié.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $user->comptable?->telephone) }}"
                                placeholder="+229 XX XX XX XX"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('telephone') border-rose-400 @enderror"/>
                            @error('telephone')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-base">save</span>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tab Mot de passe --}}
        <div x-show="tab === 'password'">
            @if(session('success_password'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl mb-5 text-sm font-semibold">
                <span class="material-symbols-outlined text-base">check_circle</span>
                {{ session('success_password') }}
            </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 md:p-6">
                <form method="POST" action="{{ route('comptable.profil.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Mot de passe actuel</label>
                            <input type="password" name="current_password"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('current_password') border-rose-400 @enderror"/>
                            @error('current_password')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Nouveau mot de passe</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('password') border-rose-400 @enderror"/>
                            @error('password')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Confirmer le mot de passe</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-base">lock_reset</span>
                            Modifier le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection