@extends('layouts.admin')

@section('title', 'Classes — EduCore')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Classes</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez les classes de l'établissement.</p>
    </div>
    <button onclick="openModal('modal-create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
        <span class="material-symbols-outlined text-base">add</span>
        Nouvelle Classe
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
    <form method="GET" action="#" class="flex flex-col sm:flex-row gap-3">
        {{-- Filtre année --}}
        <div class="flex-1">
            <label class="block text-xs font-semibold text-navy-700 mb-1.5">Année Académique</label>
            <select name="annee_id"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="1" selected>2024 - 2025 (Active)</option>
                <option value="2">2023 - 2024</option>
                <option value="3">2022 - 2023</option>
            </select>
        </div>
        {{-- Filtre niveau --}}
        <div class="flex-1">
            <label class="block text-xs font-semibold text-navy-700 mb-1.5">Niveau</label>
            <select name="niveau"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="">Tous les niveaux</option>
                <option value="6eme">6ème</option>
                <option value="5eme">5ème</option>
                <option value="4eme">4ème</option>
                <option value="3eme">3ème</option>
                <option value="2nde">2nde</option>
                <option value="1ere">1ère</option>
                <option value="terminale">Terminale</option>
            </select>
        </div>
        {{-- Filtre cycle --}}
        <div class="flex-1">
            <label class="block text-xs font-semibold text-navy-700 mb-1.5">Cycle</label>
            <select name="cycle"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="">Tous les cycles</option>
                <option value="premier">Premier cycle</option>
                <option value="second">Second cycle</option>
            </select>
        </div>
        {{-- Bouton filtrer --}}
        <div class="flex items-end">
            <button type="submit"
                class="w-full sm:w-auto px-5 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                Filtrer
            </button>
        </div>
    </form>
</div>

{{-- Tableau --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-navy-900">Classes — 2024 - 2025</h2>
            <p class="text-xs text-navy-700 mt-0.5">Année active</p>
        </div>
        <span class="text-xs text-navy-700 font-medium">6 classes</span>
    </div>

    {{-- Vue tableau md+ --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left" style="min-width:600px;">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Niveau</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Cycle</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Série</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Effectif</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">

                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">6ème A</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">6ème</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Premier</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">—</td>
                    <td class="px-6 py-4 text-sm text-navy-700">38</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-1')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-1')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">5ème B</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">5ème</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Premier</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">—</td>
                    <td class="px-6 py-4 text-sm text-navy-700">38</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-2')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-2')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">4ème C</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">4ème</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Premier</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">—</td>
                    <td class="px-6 py-4 text-sm text-navy-700">38</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-3')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-3')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">3ème L</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">3ème</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Premier</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">Série L</td>
                    <td class="px-6 py-4 text-sm text-navy-700">38</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-4')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-4')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">2nde A</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">2nde</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 text-violet-700 text-xs font-bold rounded-lg">Second</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">Série A</td>
                    <td class="px-6 py-4 text-sm text-navy-700">38</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-5')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-5')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">Tle D</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">Terminale</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 text-violet-700 text-xs font-bold rounded-lg">Second</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">Série D</td>
                    <td class="px-6 py-4 text-sm text-navy-700">38</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-6')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-6')" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Vue cartes mobile --}}
    <div class="md:hidden divide-y divide-slate-100">

        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">6ème A</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-lg">6ème</span>
                    <span class="inline-flex items-center px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-lg">Premier</span>
                </div>
            </div>
            <p class="text-xs text-navy-700">Série : —</p>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">group</span>38 élèves</span>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-1')" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                <button onclick="openModal('modal-delete-1')" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>Supprimer
                </button>
            </div>
        </div>

        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">2nde A</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-lg">2nde</span>
                    <span class="inline-flex items-center px-2 py-0.5 bg-violet-50 text-violet-700 text-[10px] font-bold rounded-lg">Second</span>
                </div>
            </div>
            <p class="text-xs text-navy-700">Série : A</p>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">group</span>38 élèves</span>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-5')" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                <button onclick="openModal('modal-delete-5')" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>Supprimer
                </button>
            </div>
        </div>

        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">Tle D</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-lg">Terminale</span>
                    <span class="inline-flex items-center px-2 py-0.5 bg-violet-50 text-violet-700 text-[10px] font-bold rounded-lg">Second</span>
                </div>
            </div>
            <p class="text-xs text-navy-700">Série : D</p>
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">group</span>38 élèves</span>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-6')" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                <button onclick="openModal('modal-delete-6')" class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>Supprimer
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ═══════ MODAL CRÉER ═══════ --}}
<div id="modal-create" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-create')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Nouvelle Classe</h3>
            <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="#" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom de la classe <span class="text-rose-500">*</span></label>
                <input type="text" name="nom" placeholder="Ex: 4ème C, Tle D, 2nde A..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Niveau <span class="text-rose-500">*</span></label>
                <select name="niveau"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Choisir un niveau --</option>
                    <option value="6eme">6ème</option>
                    <option value="5eme">5ème</option>
                    <option value="4eme">4ème</option>
                    <option value="3eme">3ème</option>
                    <option value="2nde">2nde</option>
                    <option value="1ere">1ère</option>
                    <option value="terminale">Terminale</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Cycle <span class="text-rose-500">*</span></label>
                <select name="cycle"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Choisir un cycle --</option>
                    <option value="premier">Premier cycle</option>
                    <option value="second">Second cycle</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Série</label>
                <select name="serie_id"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Aucune série (collège) --</option>
                    <option value="1">Série A</option>
                    <option value="2">Série C</option>
                    <option value="3">Série D</option>
                    <option value="4">Série G</option>
                    <option value="5">Série L</option>
                </select>
                <p class="text-xs text-slate-400 mt-1">Optionnelle pour les classes de collège.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Année Académique <span class="text-rose-500">*</span></label>
                <select name="annee_academique_id"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="1" selected>2024 - 2025 (Active)</option>
                    <option value="2">2023 - 2024</option>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════ MODAL MODIFIER 1 ═══════ --}}
<div id="modal-edit-1" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-1')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Modifier la Classe</h3>
            <button onclick="closeModal('modal-edit-1')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="#" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom de la classe <span class="text-rose-500">*</span></label>
                <input type="text" name="nom" value="6ème A"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Niveau <span class="text-rose-500">*</span></label>
                <select name="niveau"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="6eme" selected>6ème</option>
                    <option value="5eme">5ème</option>
                    <option value="4eme">4ème</option>
                    <option value="3eme">3ème</option>
                    <option value="2nde">2nde</option>
                    <option value="1ere">1ère</option>
                    <option value="terminale">Terminale</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Cycle <span class="text-rose-500">*</span></label>
                <select name="cycle"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="premier" selected>Premier cycle</option>
                    <option value="second">Second cycle</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Série</label>
                <select name="serie_id"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="" selected>-- Aucune série --</option>
                    <option value="1">Série A</option>
                    <option value="2">Série C</option>
                    <option value="3">Série D</option>
                    <option value="4">Série G</option>
                    <option value="5">Série L</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Année Académique <span class="text-rose-500">*</span></label>
                <select name="annee_academique_id"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="1" selected>2024 - 2025 (Active)</option>
                    <option value="2">2023 - 2024</option>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-1')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════ MODAL SUPPRIMER 1 ═══════ --}}
<div id="modal-delete-1" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-1')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer cette classe ?</h3>
            <p class="text-sm text-navy-700 mb-6">La classe <strong>6ème A</strong> sera définitivement supprimée. Cette action est irréversible.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-delete-1')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="#" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

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
</script>
@endsection