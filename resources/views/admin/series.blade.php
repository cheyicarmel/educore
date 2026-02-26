@extends('layouts.admin')

@section('title', 'Séries — EduCore')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Séries</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez les séries de l'établissement.</p>
    </div>
    <button onclick="openModal('modal-create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
        <span class="material-symbols-outlined text-base">add</span>
        Nouvelle Série
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
        <h2 class="text-base font-bold text-navy-900">Liste des Séries</h2>
        <span class="text-xs text-navy-700 font-medium">{{ $series->count() }} {{ Str::plural('série', $series->count()) }}</span>
    </div>

    @if($series->isEmpty())
    <div class="p-12 text-center">
        <span class="material-symbols-outlined text-slate-300 text-5xl">category</span>
        <p class="text-sm font-semibold text-slate-400 mt-3">Aucune série créée.</p>
        <button onclick="openModal('modal-create')" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>Créer la première série
        </button>
    </div>
    @else

    {{-- Vue tableau md+ --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left" style="min-width:500px;">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Libellé</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($series as $serie)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg">{{ $serie->code }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-navy-900">{{ $serie->libelle }}</td>
                    <td class="px-6 py-4 text-sm text-navy-700">{{ $serie->description ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-{{ $serie->id }}')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-{{ $serie->id }}')"
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
        @foreach($series as $serie)
        <div class="p-4 space-y-3">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg">{{ $serie->code }}</span>
                <span class="text-sm font-semibold text-navy-900">{{ $serie->libelle }}</span>
            </div>
            @if($serie->description)
            <p class="text-xs text-navy-700">{{ $serie->description }}</p>
            @endif
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-{{ $serie->id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>Modifier
                </button>
                <button onclick="openModal('modal-delete-{{ $serie->id }}')"
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
            <h3 class="text-base font-bold text-navy-900">Nouvelle Série</h3>
            <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.series.store') }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Code <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="Ex: A, C, D..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                @error('code')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Libellé <span class="text-rose-500">*</span></label>
                <input type="text" name="libelle" value="{{ old('libelle') }}" placeholder="Ex: Série A"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                @error('libelle')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Type <span class="text-rose-500">*</span></label>
                <select name="type"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="">-- Choisir un type --</option>
                    <option value="litteraire" {{ old('type') == 'litteraire' ? 'selected' : '' }}>Littéraire</option>
                    <option value="scientifique" {{ old('type') == 'scientifique' ? 'selected' : '' }}>Scientifique</option>
                </select>
                @error('type')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Description</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="Ex: Lettres et Sciences Humaines"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                @error('description')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Créer</button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════ MODALS DYNAMIQUES PAR SÉRIE ═══════ --}}
@foreach($series as $serie)

{{-- Modal Modifier --}}
<div id="modal-edit-{{ $serie->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-{{ $serie->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Modifier la Série</h3>
            <button onclick="closeModal('modal-edit-{{ $serie->id }}')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.series.update', $serie) }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Code <span class="text-rose-500">*</span></label>
                <input type="text" name="code" value="{{ $serie->code }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Libellé <span class="text-rose-500">*</span></label>
                <input type="text" name="libelle" value="{{ $serie->libelle }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Type <span class="text-rose-500">*</span></label>
                <select name="type"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                    <option value="litteraire" {{ $serie->type == 'litteraire' ? 'selected' : '' }}>Littéraire</option>
                    <option value="scientifique" {{ $serie->type == 'scientifique' ? 'selected' : '' }}>Scientifique</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Description</label>
                <input type="text" name="description" value="{{ $serie->description }}"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-{{ $serie->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Supprimer --}}
<div id="modal-delete-{{ $serie->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-{{ $serie->id }}')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer cette série ?</h3>
            <p class="text-sm text-navy-700 mb-6">La série <strong>{{ $serie->libelle }}</strong> sera définitivement supprimée. Cette action est irréversible.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-delete-{{ $serie->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                <form method="POST" action="{{ route('admin.series.destroy', $serie) }}" class="flex-1">
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