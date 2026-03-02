@extends('layouts.admin')

@section('title', 'Élèves — EduCore')

@section('content')

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Élèves</h1>
            <p class="text-sm text-navy-700 mt-1">Gérez les élèves et leurs inscriptions.</p>
        </div>
        <button onclick="openModal('modal-create')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
            <span class="material-symbols-outlined text-base">add</span>
            Nouvel Élève
        </button>
    </div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-5 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-rose-500">error</span>
        <p class="text-sm font-semibold text-rose-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5">
        <form method="GET" action="{{ route('admin.eleves.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-navy-700 mb-1.5">Année Académique</label>
                <select name="annee_id"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @foreach($annees as $annee)
                    <option value="{{ $annee->id }}" {{ $anneeSelectionnee?->id == $annee->id ? 'selected' : '' }}>
                        {{ $annee->libelle }}{{ $annee->estActive() ? ' (Active)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-navy-700 mb-1.5">Classe</label>
                <select name="classe_id"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">Toutes les classes</option>
                    @foreach($classes as $classe)
                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                        {{ $classe->nom }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-navy-700 mb-1.5">Statut</label>
                <select name="statut"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">Tous</option>
                    <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="passant" {{ request('statut') == 'passant' ? 'selected' : '' }}>Passant</option>
                    <option value="doublant" {{ request('statut') == 'doublant' ? 'selected' : '' }}>Doublant</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-navy-900">Élèves — {{ $anneeSelectionnee?->libelle ?? 'Aucune année' }}</h2>
                @if($anneeSelectionnee?->estActive())
                <p class="text-xs text-emerald-600 font-semibold mt-0.5">Année active</p>
                @endif
            </div>
            <span class="text-xs text-navy-700 font-medium">{{ $eleves->count() }} élève{{ $eleves->count() > 1 ? 's' : '' }}</span>
        </div>

        @if(!$anneeSelectionnee)
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-5xl">calendar_today</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucune année académique configurée.</p>
        </div>
        @elseif($eleves->isEmpty())
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-5xl">group</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucun élève inscrit pour cette période.</p>
            <button onclick="openModal('modal-create')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">add</span>Inscrire le premier élève
            </button>
        </div>
        @else

        {{-- Vue tableau md+ --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left" style="min-width:750px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Matricule</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Frais annuels</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($eleves as $eleve)
                    @php
                        $inscription = $eleve->inscriptions->first();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-sm font-bold text-primary">
                                        {{ strtoupper(substr($eleve->user->prenom, 0, 1) . substr($eleve->user->nom, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-navy-900">{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</p>
                                    <p class="text-xs text-navy-700">{{ $eleve->sexe === 'M' ? 'Masculin' : 'Féminin' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-navy-700 font-mono">{{ $eleve->numero_matricule }}</td>
                        <td class="px-6 py-4 text-sm text-navy-700">{{ $inscription?->classe?->nom ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-navy-700">
                            {{ $inscription ? number_format($inscription->frais_annuels, 0, ',', ' ') . ' FCFA' : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($inscription?->statut === 'actif')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Actif
                            </span>
                            @elseif($inscription?->statut === 'passant')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-blue-700 bg-blue-100 rounded-full uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Passant
                            </span>
                            @elseif($inscription?->statut === 'doublant')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Doublant
                            </span>
                            @else
                            <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openModal('modal-edit-{{ $eleve->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                    <span class="material-symbols-outlined text-base">edit</span>
                                </button>
                                <button onclick="openModal('modal-delete-{{ $eleve->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Vue cartes mobile --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($eleves as $eleve)
            @php $inscription = $eleve->inscriptions->first(); @endphp
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-primary">
                                {{ strtoupper(substr($eleve->user->prenom, 0, 1) . substr($eleve->user->nom, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-navy-900">{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $eleve->numero_matricule }}</p>
                        </div>
                    </div>
                    @if($inscription?->statut === 'actif')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Actif
                    </span>
                    @elseif($inscription?->statut === 'passant')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-blue-700 bg-blue-100 rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>Passant
                    </span>
                    @elseif($inscription?->statut === 'doublant')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-amber-700 bg-amber-100 rounded-full uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Doublant
                    </span>
                    @endif
                </div>
                <div class="flex flex-wrap gap-3 text-xs text-navy-700">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">school</span>{{ $inscription?->classe?->nom ?? '—' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">payments</span>
                        {{ $inscription ? number_format($inscription->frais_annuels, 0, ',', ' ') . ' FCFA' : '—' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <button onclick="openModal('modal-edit-{{ $eleve->id }}')"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span>Modifier
                    </button>
                    <button onclick="openModal('modal-delete-{{ $eleve->id }}')"
                        class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span>Supprimer
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ═══════ MODAL CRÉER ═══════ --}}
    <div id="modal-create" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-create')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-base font-bold text-navy-900">Nouvel Élève</h3>
                <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.eleves.store') }}" class="p-5 space-y-4">
                @csrf
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-xs text-blue-700 font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">info</span>
                        Un compte élève sera créé et les identifiants envoyés à l'adresse email du parent.
                    </p>
                </div>
                <p class="text-xs font-bold text-navy-700 uppercase tracking-wider">Informations de l'élève</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Ex: Amavi"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        @error('nom')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Prénom <span class="text-rose-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Ex: Kokou"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        @error('prenom')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date de naissance</label>
                        <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Sexe <span class="text-rose-500">*</span></label>
                        <select name="sexe"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                            <option value="">-- Choisir --</option>
                            <option value="M" {{ old('sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                        @error('sexe')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <p class="text-xs font-bold text-navy-700 uppercase tracking-wider pt-2">Inscription</p>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Classe <span class="text-rose-500">*</span></label>
                    <select name="classe_id"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">-- Choisir une classe --</option>
                        @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                        @endforeach
                    </select>
                    @error('classe_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Frais annuels (FCFA) <span class="text-rose-500">*</span></label>
                    <input type="number" name="frais_annuels" value="{{ old('frais_annuels') }}" placeholder="Ex: 150000" min="0"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    @error('frais_annuels')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <p class="text-xs font-bold text-navy-700 uppercase tracking-wider pt-2">Contact Parent</p>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Email du parent <span class="text-rose-500">*</span></label>
                    <input type="email" name="email_parent" value="{{ old('email_parent') }}" placeholder="Ex: parent@gmail.com"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    <p class="text-xs text-slate-400 mt-1">Les identifiants de connexion seront envoyés à cette adresse.</p>
                    @error('email_parent')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Téléphone du parent</label>
                    <input type="text" name="telephone_parent" value="{{ old('telephone_parent') }}" placeholder="Ex: +229 97 00 00 01"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Inscrire</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════ MODALS DYNAMIQUES PAR ÉLÈVE ═══════ --}}
    @foreach($eleves as $eleve)
        @php $inscription = $eleve->inscriptions->first(); @endphp

        {{-- Modal Modifier --}}
        <div id="modal-edit-{{ $eleve->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-{{ $eleve->id }}')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                    <h3 class="text-base font-bold text-navy-900">Modifier l'Élève</h3>
                    <button onclick="closeModal('modal-edit-{{ $eleve->id }}')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.eleves.update', $eleve) }}" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <p class="text-xs font-bold text-navy-700 uppercase tracking-wider">Informations de l'élève</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom <span class="text-rose-500">*</span></label>
                            <input type="text" name="nom" value="{{ $eleve->user->nom }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Prénom <span class="text-rose-500">*</span></label>
                            <input type="text" name="prenom" value="{{ $eleve->user->prenom }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date de naissance</label>
                            <input type="date" name="date_naissance" value="{{ $eleve->date_naissance?->format('Y-m-d') }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-navy-900 mb-1.5">Sexe <span class="text-rose-500">*</span></label>
                            <select name="sexe"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                                <option value="M" {{ $eleve->sexe == 'M' ? 'selected' : '' }}>Masculin</option>
                                <option value="F" {{ $eleve->sexe == 'F' ? 'selected' : '' }}>Féminin</option>
                            </select>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-navy-700 uppercase tracking-wider pt-2">Contact Parent</p>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Email du parent <span class="text-rose-500">*</span></label>
                        <input type="email" name="email_parent" value="{{ $eleve->email_parent }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy-900 mb-1.5">Téléphone du parent</label>
                        <input type="text" name="telephone_parent" value="{{ $eleve->telephone_parent }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" onclick="closeModal('modal-edit-{{ $eleve->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                        <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Supprimer --}}
        <div id="modal-delete-{{ $eleve->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-{{ $eleve->id }}')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
                    </div>
                    <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer cet élève ?</h3>
                    <p class="text-sm text-navy-700 mb-6"><strong>{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</strong> et son compte utilisateur seront définitivement supprimés. Cette action est irréversible.</p>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closeModal('modal-delete-{{ $eleve->id }}')"
                            class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                        <form method="POST" action="{{ route('admin.eleves.destroy', $eleve) }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2.5 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Pagination --}}
    @if($eleves->hasPages())
    <div class="mt-5 flex items-center justify-between">
        <p class="text-xs text-navy-700">
            Affichage de <strong>{{ $eleves->firstItem() }}</strong> à <strong>{{ $eleves->lastItem() }}</strong>
            sur <strong>{{ $eleves->total() }}</strong> élèves
        </p>
        <div class="flex items-center gap-1">
            {{-- Précédent --}}
            @if($eleves->onFirstPage())
            <span class="px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">
                ←
            </span>
            @else
            <a href="{{ $eleves->previousPageUrl() }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                ←
            </a>
            @endif

            {{-- Pages --}}
            @foreach($eleves->getUrlRange(max(1, $eleves->currentPage()-2), min($eleves->lastPage(), $eleves->currentPage()+2)) as $page => $url)
            @if($page == $eleves->currentPage())
            <span class="px-3 py-2 text-xs font-bold text-white bg-primary rounded-xl">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">{{ $page }}</a>
            @endif
            @endforeach

            {{-- Suivant --}}
            @if($eleves->hasMorePages())
            <a href="{{ $eleves->nextPageUrl() }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                →
            </a>
            @else
            <span class="px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">
                →
            </span>
            @endif
        </div>
    </div>
    @endif

@endsection

@section('scripts')
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="modal-"]').forEach(modal => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = '';
                });
            }
        });

        @if($errors->any())
            openModal('modal-create');
        @endif
    </script>
@endsection