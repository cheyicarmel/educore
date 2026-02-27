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
    <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-navy-700 mb-1.5">Année Académique</label>
            <select name="annee_id"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                @foreach($annees as $annee)
                <option value="{{ $annee->id }}" {{ $anneeSelectionnee?->id == $annee->id ? 'selected' : '' }}>
                    {{ $annee->libelle }}{{ $annee->est_active ? ' (Active)' : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-navy-700 mb-1.5">Niveau</label>
            <select name="niveau"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="">Tous les niveaux</option>
                @foreach(['6eme','5eme','4eme','3eme','2nde','1ere','terminale'] as $niv)
                <option value="{{ $niv }}" {{ request('niveau') == $niv ? 'selected' : '' }}>
                    {{ ucfirst($niv) }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-navy-700 mb-1.5">Cycle</label>
            <select name="cycle"
                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="">Tous les cycles</option>
                <option value="premier" {{ request('cycle') == 'premier' ? 'selected' : '' }}>Premier cycle</option>
                <option value="second" {{ request('cycle') == 'second' ? 'selected' : '' }}>Second cycle</option>
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
            <h2 class="text-base font-bold text-navy-900">
                Classes — {{ $anneeSelectionnee?->libelle ?? 'Aucune année' }}
            </h2>
            @if($anneeSelectionnee?->est_active)
            <p class="text-xs text-emerald-600 font-semibold mt-0.5">Année active</p>
            @endif
        </div>
        <span class="text-xs text-navy-700 font-medium">{{ $classes->count() }} classe{{ $classes->count() > 1 ? 's' : '' }}</span>
    </div>

    @if(!$anneeSelectionnee)
    <div class="p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">calendar_today</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucune année académique configurée.</p>
    </div>
    @elseif($classes->isEmpty())
    <div class="p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">school</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucune classe pour cette période.</p>
        <button onclick="openModal('modal-create')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>Créer la première classe
        </button>
    </div>
    @else

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
                @foreach($classes as $classe)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4"><span class="text-sm font-bold text-navy-900">{{ $classe->nom }}</span></td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">{{ $classe->niveau }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($classe->cycle === 'premier')
                        <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg">Premier</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 text-violet-700 text-xs font-bold rounded-lg">Second</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $classe->serie->libelle ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $classe->effectif }} élève{{ $classe->effectif > 1 ? 's' : '' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-{{ $classe->id }}')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-{{ $classe->id }}')"
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
        @foreach($classes as $classe)
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">{{ $classe->nom }}</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-lg">{{ $classe->niveau }}</span>
                    @if($classe->cycle === 'premier')
                    <span class="inline-flex items-center px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-lg">Premier</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 bg-violet-50 text-violet-700 text-[10px] font-bold rounded-lg">Second</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3 text-xs text-navy-700">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">category</span>
                    {{ $classe->serie->libelle ?? '—' }}
                </span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">group</span>
                    {{ $classe->effectif }} élève{{ $classe->effectif > 1 ? 's' : '' }}
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-{{ $classe->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                <button onclick="openModal('modal-delete-{{ $classe->id }}')"
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
            <h3 class="text-base font-bold text-navy-900">Nouvelle Classe</h3>
            <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.classes.store') }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom de la classe <span class="text-rose-500">*</span></label>
                <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Ex: 4ème C, Tle D..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                @error('nom')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Niveau <span class="text-rose-500">*</span></label>
                <select name="niveau" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Choisir un niveau --</option>
                    @foreach(['6eme' => '6ème', '5eme' => '5ème', '4eme' => '4ème', '3eme' => '3ème', '2nde' => '2nde', '1ere' => '1ère', 'terminale' => 'Terminale'] as $val => $label)
                    <option value="{{ $val }}" {{ old('niveau') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('niveau')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Cycle <span class="text-rose-500">*</span></label>
                <select name="cycle" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Choisir un cycle --</option>
                    <option value="premier" {{ old('cycle') == 'premier' ? 'selected' : '' }}>Premier cycle</option>
                    <option value="second" {{ old('cycle') == 'second' ? 'selected' : '' }}>Second cycle</option>
                </select>
                @error('cycle')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Série <span class="text-rose-500">*</span></label>
                <select name="serie_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Choisir une série --</option>
                    @foreach($series as $serie)
                    <option value="{{ $serie->id }}" {{ old('serie_id') == $serie->id ? 'selected' : '' }}>
                        {{ $serie->libelle }}
                    </option>
                    @endforeach
                </select>
                @error('serie_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Année Académique <span class="text-rose-500">*</span></label>
                <select name="annee_academique_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @foreach($annees as $annee)
                    <option value="{{ $annee->id }}" {{ (old('annee_academique_id', $anneeSelectionnee?->id)) == $annee->id ? 'selected' : '' }}>
                        {{ $annee->libelle }}{{ $annee->est_active ? ' (Active)' : '' }}
                    </option>
                    @endforeach
                </select>
                @error('annee_academique_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════ MODALS DYNAMIQUES PAR CLASSE ═══════ --}}
@foreach($classes as $classe)

{{-- Modal Modifier --}}
<div id="modal-edit-{{ $classe->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-{{ $classe->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Modifier la Classe</h3>
            <button onclick="closeModal('modal-edit-{{ $classe->id }}')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.classes.update', $classe) }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Nom de la classe <span class="text-rose-500">*</span></label>
                <input type="text" name="nom" value="{{ $classe->nom }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Niveau <span class="text-rose-500">*</span></label>
                <select name="niveau" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @foreach(['6eme' => '6ème', '5eme' => '5ème', '4eme' => '4ème', '3eme' => '3ème', '2nde' => '2nde', '1ere' => '1ère', 'terminale' => 'Terminale'] as $val => $label)
                    <option value="{{ $val }}" {{ $classe->niveau == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Cycle <span class="text-rose-500">*</span></label>
                <select name="cycle" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="premier" {{ $classe->cycle == 'premier' ? 'selected' : '' }}>Premier cycle</option>
                    <option value="second" {{ $classe->cycle == 'second' ? 'selected' : '' }}>Second cycle</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Série <span class="text-rose-500">*</span></label>
                <select name="serie_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @foreach($series as $serie)
                    <option value="{{ $serie->id }}" {{ $classe->serie_id == $serie->id ? 'selected' : '' }}>
                        {{ $serie->libelle }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Année Académique <span class="text-rose-500">*</span></label>
                <select name="annee_academique_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    @foreach($annees as $annee)
                    <option value="{{ $annee->id }}" {{ $classe->annee_academique_id == $annee->id ? 'selected' : '' }}>
                        {{ $annee->libelle }}{{ $annee->est_active ? ' (Active)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-{{ $classe->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Supprimer --}}
<div id="modal-delete-{{ $classe->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-{{ $classe->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer cette classe ?</h3>
            <p class="text-sm text-navy-700 mb-6">La classe <strong>{{ $classe->nom }}</strong> sera définitivement supprimée. Cette action est irréversible.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-delete-{{ $classe->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="{{ route('admin.classes.destroy', $classe) }}" class="flex-1">
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