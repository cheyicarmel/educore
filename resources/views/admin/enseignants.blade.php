@extends('layouts.admin')

@section('title', 'Enseignants — EduCore')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Enseignants</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez les enseignants et leurs accès à la plateforme.</p>
    </div>
    <button onclick="openModal('modal-create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
        <span class="material-symbols-outlined text-base">add</span>
        Nouvel Enseignant
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
        <h2 class="text-base font-bold text-navy-900">Liste des Enseignants</h2>
        <span class="text-xs text-navy-700 font-medium">{{ $enseignants->count() }} enseignant{{ $enseignants->count() > 1 ? 's' : '' }}</span>
    </div>

    @if($enseignants->isEmpty())
    <div class="p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">person</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucun enseignant enregistré.</p>
        <button onclick="openModal('modal-create')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>Ajouter le premier enseignant
        </button>
    </div>
    @else

    {{-- Vue tableau md+ --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left" style="min-width:700px;">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Enseignant</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Spécialité</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Téléphone</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($enseignants as $enseignant)
                @php $actif = $enseignant->user->est_actif; @endphp
                <tr class="hover:bg-slate-50 transition-colors {{ !$actif ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $actif ? 'bg-primary/10' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
                                <span class="text-sm font-bold {{ $actif ? 'text-primary' : 'text-slate-400' }}">
                                    {{ strtoupper(substr($enseignant->user->prenom, 0, 1) . substr($enseignant->user->nom, 0, 1)) }}
                                </span>
                            </div>
                            <p class="text-sm font-bold text-navy-900">{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $enseignant->user->email }}</td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $enseignant->specialite ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $enseignant->telephone ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @if($actif)
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
                            <button onclick="openModal('modal-edit-{{ $enseignant->id }}')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            @if($actif)
                            <button onclick="openModal('modal-toggle-{{ $enseignant->id }}')"
                                class="p-1.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Désactiver">
                                <span class="material-symbols-outlined text-base">block</span>
                            </button>
                            @else
                            <button onclick="openModal('modal-toggle-{{ $enseignant->id }}')"
                                class="p-1.5 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors" title="Activer">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                            </button>
                            @endif
                            <button onclick="openModal('modal-delete-{{ $enseignant->id }}')"
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
        @foreach($enseignants as $enseignant)
        @php $actif = $enseignant->user->est_actif; @endphp
        <div class="p-4 space-y-3 {{ !$actif ? 'opacity-60' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full {{ $actif ? 'bg-primary/10' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold {{ $actif ? 'text-primary' : 'text-slate-400' }}">
                            {{ strtoupper(substr($enseignant->user->prenom, 0, 1) . substr($enseignant->user->nom, 0, 1)) }}
                        </span>
                    </div>
                    <span class="text-sm font-bold text-navy-900">{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</span>
                </div>
                @if($actif)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Actif
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Inactif
                </span>
                @endif
            </div>
            <div class="flex flex-wrap gap-3 text-xs text-navy-700">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">mail</span>{{ $enseignant->user->email }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">school</span>{{ $enseignant->specialite ?? '—' }}
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-{{ $enseignant->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                @if($actif)
                <button onclick="openModal('modal-toggle-{{ $enseignant->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">block</span>Désactiver
                </button>
                @else
                <button onclick="openModal('modal-toggle-{{ $enseignant->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">check_circle</span>Activer
                </button>
                @endif
                <button onclick="openModal('modal-delete-{{ $enseignant->id }}')"
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
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Nouvel Enseignant</h3>
            <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.enseignants.store') }}" class="p-5 space-y-4">
            @csrf
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <p class="text-xs text-blue-700 font-semibold flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Un compte utilisateur sera automatiquement créé et les identifiants envoyés par email.
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
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Spécialité</label>
                <input type="text" name="specialite" value="{{ old('specialite') }}" placeholder="Ex: Mathématiques"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="Ex: +229 97 00 00 01"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════ MODALS DYNAMIQUES PAR ENSEIGNANT ═══════ --}}
@foreach($enseignants as $enseignant)
@php $actif = $enseignant->user->est_actif; @endphp

{{-- Modal Modifier --}}
<div id="modal-edit-{{ $enseignant->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-{{ $enseignant->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Modifier l'Enseignant</h3>
            <button onclick="closeModal('modal-edit-{{ $enseignant->id }}')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.enseignants.update', $enseignant) }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom <span class="text-rose-500">*</span></label>
                    <input type="text" name="nom" value="{{ $enseignant->user->nom }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Prénom <span class="text-rose-500">*</span></label>
                    <input type="text" name="prenom" value="{{ $enseignant->user->prenom }}"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ $enseignant->user->email }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Spécialité</label>
                <input type="text" name="specialite" value="{{ $enseignant->specialite }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Téléphone</label>
                <input type="text" name="telephone" value="{{ $enseignant->telephone }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-{{ $enseignant->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Toggle --}}
<div id="modal-toggle-{{ $enseignant->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-toggle-{{ $enseignant->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            @if($actif)
            <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-amber-500 text-2xl">block</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Désactiver cet enseignant ?</h3>
            <p class="text-sm text-navy-700 mb-6"><strong>{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</strong> ne pourra plus se connecter à la plateforme.</p>
            @else
            <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-emerald-500 text-2xl">check_circle</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Activer cet enseignant ?</h3>
            <p class="text-sm text-navy-700 mb-6"><strong>{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</strong> pourra à nouveau se connecter à la plateforme.</p>
            @endif
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-toggle-{{ $enseignant->id }}')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="{{ route('admin.enseignants.toggle', $enseignant) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="w-full py-2.5 text-sm font-bold text-white {{ $actif ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }} rounded-xl transition-colors">
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Supprimer --}}
<div id="modal-delete-{{ $enseignant->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-{{ $enseignant->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer cet enseignant ?</h3>
            <p class="text-sm text-navy-700 mb-6"><strong>{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</strong> et son compte utilisateur seront définitivement supprimés. Cette action est irréversible.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-delete-{{ $enseignant->id }}')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="{{ route('admin.enseignants.destroy', $enseignant) }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

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