@extends('layouts.admin')

@section('title', 'Comptes & Accès — EduCore')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Comptes & Accès</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez les comptes administrateurs, super administrateurs et comptables.</p>
    </div>
    <button onclick="openModal('modal-create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
        <span class="material-symbols-outlined text-base">add</span>
        Nouveau Compte
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

{{-- Tableau --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-base font-bold text-navy-900">Liste des Comptes</h2>
        <span class="text-xs text-navy-700 font-medium">{{ $admins->count() }} compte{{ $admins->count() > 1 ? 's' : '' }}</span>
    </div>

    @if($admins->isEmpty())
    <div class="p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">admin_panel_settings</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucun compte trouvé.</p>
    </div>
    @else

    {{-- Vue tableau md+ --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left" style="min-width:600px;">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Compte</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($admins as $admin)
                @php
                    $estMoi = $admin->id === auth()->id();
                    $roleConfig = match($admin->role) {
                        'superadmin' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'badge' => 'bg-violet-100 text-violet-700', 'icon' => 'shield', 'label' => 'Super Admin'],
                        'comptable'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'account_balance_wallet', 'label' => 'Comptable'],
                        default      => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'badge' => 'bg-blue-50 text-blue-700', 'icon' => 'admin_panel_settings', 'label' => 'Admin'],
                    };
                @endphp
                <tr class="hover:bg-slate-50 transition-colors {{ !$admin->est_actif ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $roleConfig['bg'] }} flex items-center justify-center shrink-0">
                                <span class="text-sm font-bold {{ $roleConfig['text'] }}">
                                    {{ strtoupper(substr($admin->prenom, 0, 1) . substr($admin->nom, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-navy-900">{{ $admin->prenom }} {{ $admin->nom }}</p>
                                @if($estMoi)
                                <p class="text-xs text-primary font-semibold">Vous</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $admin->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 {{ $roleConfig['badge'] }} text-xs font-bold rounded-lg">
                            <span class="material-symbols-outlined text-sm">{{ $roleConfig['icon'] }}</span>
                            {{ $roleConfig['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($admin->est_actif)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Actif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Inactif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-{{ $admin->id }}')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            @if(!$estMoi)
                                @if($admin->est_actif)
                                <button onclick="openModal('modal-toggle-{{ $admin->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Désactiver">
                                    <span class="material-symbols-outlined text-base">block</span>
                                </button>
                                @else
                                <button onclick="openModal('modal-toggle-{{ $admin->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors" title="Activer">
                                    <span class="material-symbols-outlined text-base">check_circle</span>
                                </button>
                                @endif
                                <button onclick="openModal('modal-delete-{{ $admin->id }}')"
                                    class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Supprimer">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            @else
                                <span class="p-1.5 text-slate-200 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-base">block</span>
                                </span>
                                <span class="p-1.5 text-slate-200 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Vue cartes mobile --}}
    <div class="md:hidden divide-y divide-slate-100">
        @foreach($admins as $admin)
        @php
            $estMoi = $admin->id === auth()->id();
            $roleConfig = match($admin->role) {
                'superadmin' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'badge' => 'bg-violet-100 text-violet-700', 'icon' => 'shield', 'label' => 'Super Admin'],
                'comptable'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'account_balance_wallet', 'label' => 'Comptable'],
                default      => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'badge' => 'bg-blue-50 text-blue-700', 'icon' => 'admin_panel_settings', 'label' => 'Admin'],
            };
        @endphp
        <div class="p-4 space-y-3 {{ !$admin->est_actif ? 'opacity-60' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full {{ $roleConfig['bg'] }} flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold {{ $roleConfig['text'] }}">
                            {{ strtoupper(substr($admin->prenom, 0, 1) . substr($admin->nom, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-navy-900">{{ $admin->prenom }} {{ $admin->nom }}</p>
                        <p class="text-xs text-navy-700">{{ $admin->email }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 {{ $roleConfig['badge'] }} text-xs font-bold rounded-lg">
                    <span class="material-symbols-outlined text-sm">{{ $roleConfig['icon'] }}</span>
                    {{ $roleConfig['label'] }}
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-{{ $admin->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                @if(!$estMoi)
                @if($admin->est_actif)
                <button onclick="openModal('modal-toggle-{{ $admin->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">block</span>Désactiver
                </button>
                @else
                <button onclick="openModal('modal-toggle-{{ $admin->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">check_circle</span>Activer
                </button>
                @endif
                <button onclick="openModal('modal-delete-{{ $admin->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>Supprimer
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ═══════ MODAL CRÉER ═══════ --}}
<div id="modal-create" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-create')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Nouveau Compte</h3>
            <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.administrateurs.store') }}" class="p-5 space-y-4">
            @csrf
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-xs text-blue-700 font-semibold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Un compte sera créé et les identifiants envoyés par email.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom <span class="text-rose-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Ex: Agbeko"
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
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Ex: k.agbeko@educore.com"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                @error('email')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Rôle <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center gap-1.5 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
                        <input type="radio" name="role" value="admin" {{ old('role', 'admin') == 'admin' ? 'checked' : '' }} class="accent-primary"/>
                        <span class="material-symbols-outlined text-blue-600 text-xl">admin_panel_settings</span>
                        <div class="text-center">
                            <p class="text-xs font-bold text-navy-900">Admin</p>
                            <p class="text-[10px] text-slate-400">Standard</p>
                        </div>
                    </label>
                    <label class="flex flex-col items-center gap-1.5 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
                        <input type="radio" name="role" value="superadmin" {{ old('role') == 'superadmin' ? 'checked' : '' }} class="accent-primary"/>
                        <span class="material-symbols-outlined text-violet-600 text-xl">shield</span>
                        <div class="text-center">
                            <p class="text-xs font-bold text-navy-900">Super Admin</p>
                            <p class="text-[10px] text-slate-400">Complet</p>
                        </div>
                    </label>
                    <label class="flex flex-col items-center gap-1.5 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
                        <input type="radio" name="role" value="comptable" {{ old('role') == 'comptable' ? 'checked' : '' }} class="accent-primary"/>
                        <span class="material-symbols-outlined text-emerald-600 text-xl">account_balance_wallet</span>
                        <div class="text-center">
                            <p class="text-xs font-bold text-navy-900">Comptable</p>
                            <p class="text-[10px] text-slate-400">Finance</p>
                        </div>
                    </label>
                </div>
                @error('role')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════ MODALS DYNAMIQUES ═══════ --}}
@foreach($admins as $admin)
@php
    $estMoi = $admin->id === auth()->id();
    $roleConfig = match($admin->role) {
        'superadmin' => ['bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'badge' => 'bg-violet-100 text-violet-700', 'icon' => 'shield', 'label' => 'Super Admin'],
        'comptable'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-700', 'icon' => 'account_balance_wallet', 'label' => 'Comptable'],
        default      => ['bg' => 'bg-primary/10', 'text' => 'text-primary', 'badge' => 'bg-blue-50 text-blue-700', 'icon' => 'admin_panel_settings', 'label' => 'Admin'],
    };
@endphp

{{-- Modal Modifier --}}
<div id="modal-edit-{{ $admin->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-{{ $admin->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Modifier le Compte</h3>
            <button onclick="closeModal('modal-edit-{{ $admin->id }}')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.administrateurs.update', $admin) }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom <span class="text-rose-500">*</span></label>
                    <input type="text" name="nom" value="{{ $admin->nom }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Prénom <span class="text-rose-500">*</span></label>
                    <input type="text" name="prenom" value="{{ $admin->prenom }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ $admin->email }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Rôle <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center gap-1.5 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer {{ $estMoi ? 'opacity-50 cursor-not-allowed' : 'hover:border-primary/50' }} transition-colors">
                        <input type="radio" name="role" value="admin" {{ $admin->role == 'admin' ? 'checked' : '' }} {{ $estMoi ? 'disabled' : '' }} class="accent-primary"/>
                        <span class="material-symbols-outlined text-blue-600 text-xl">admin_panel_settings</span>
                        <div class="text-center">
                            <p class="text-xs font-bold text-navy-900">Admin</p>
                            <p class="text-[10px] text-slate-400">Standard</p>
                        </div>
                    </label>
                    <label class="flex flex-col items-center gap-1.5 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer {{ $estMoi ? 'opacity-50 cursor-not-allowed' : 'hover:border-primary/50' }} transition-colors">
                        <input type="radio" name="role" value="superadmin" {{ $admin->role == 'superadmin' ? 'checked' : '' }} {{ $estMoi ? 'disabled' : '' }} class="accent-primary"/>
                        <span class="material-symbols-outlined text-violet-600 text-xl">shield</span>
                        <div class="text-center">
                            <p class="text-xs font-bold text-navy-900">Super Admin</p>
                            <p class="text-[10px] text-slate-400">Complet</p>
                        </div>
                    </label>
                    <label class="flex flex-col items-center gap-1.5 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer {{ $estMoi ? 'opacity-50 cursor-not-allowed' : 'hover:border-primary/50' }} transition-colors">
                        <input type="radio" name="role" value="comptable" {{ $admin->role == 'comptable' ? 'checked' : '' }} {{ $estMoi ? 'disabled' : '' }} class="accent-primary"/>
                        <span class="material-symbols-outlined text-emerald-600 text-xl">account_balance_wallet</span>
                        <div class="text-center">
                            <p class="text-xs font-bold text-navy-900">Comptable</p>
                            <p class="text-[10px] text-slate-400">Finance</p>
                        </div>
                    </label>
                </div>
                @if($estMoi)
                <p class="text-xs text-slate-400 mt-1">Vous ne pouvez pas modifier votre propre rôle.</p>
                @endif
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-{{ $admin->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

@if(!$estMoi)
{{-- Modal Toggle --}}
<div id="modal-toggle-{{ $admin->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-toggle-{{ $admin->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full {{ $admin->est_actif ? 'bg-amber-100' : 'bg-emerald-100' }} flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined {{ $admin->est_actif ? 'text-amber-500' : 'text-emerald-500' }} text-2xl">
                    {{ $admin->est_actif ? 'block' : 'check_circle' }}
                </span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">{{ $admin->est_actif ? 'Désactiver' : 'Activer' }} ce compte ?</h3>
            <p class="text-sm text-navy-700 mb-6">
                <strong>{{ $admin->prenom }} {{ $admin->nom }}</strong>
                {{ $admin->est_actif ? 'ne pourra plus se connecter à EduCore.' : 'pourra à nouveau se connecter à EduCore.' }}
            </p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-toggle-{{ $admin->id }}')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="{{ route('admin.administrateurs.toggle', $admin) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full py-2.5 text-sm font-bold text-white {{ $admin->est_actif ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} rounded-xl transition-colors">
                        {{ $admin->est_actif ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Supprimer --}}
<div id="modal-delete-{{ $admin->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-{{ $admin->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer ce compte ?</h3>
            <p class="text-sm text-navy-700 mb-6">Le compte de <strong>{{ $admin->prenom }} {{ $admin->nom }}</strong> sera définitivement supprimé. Cette action est irréversible.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-delete-{{ $admin->id }}')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="{{ route('admin.administrateurs.destroy', $admin) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endforeach

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