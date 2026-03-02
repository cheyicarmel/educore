@extends('layouts.admin')

@section('title', 'Attributions — EduCore')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Attributions</h1>
        <p class="text-sm text-navy-700 mt-1">Assignez les enseignants aux classes et matières.</p>
    </div>
    <form method="GET" action="{{ route('admin.attributions.index') }}" class="shrink-0">
        <select name="annee_id" onchange="this.form.submit()"
            class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm text-navy-900 font-semibold focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all shadow-sm">
            @foreach($annees as $annee)
            <option value="{{ $annee->id }}" {{ $anneeSelectionnee?->id == $annee->id ? 'selected' : '' }}>
                {{ $annee->libelle }}{{ $annee->estActive() ? ' (Active)' : '' }}
            </option>
            @endforeach
        </select>
    </form>
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

@if(!$anneeSelectionnee)
<div class="p-12 text-center bg-white rounded-2xl border border-slate-200">
    <span class="material-symbols-outlined text-slate-300 text-5xl">calendar_today</span>
    <p class="text-sm font-semibold text-slate-400 mt-3">Aucune année académique configurée.</p>
</div>
@elseif($enseignants->isEmpty())
<div class="p-12 text-center bg-white rounded-2xl border border-slate-200">
    <span class="material-symbols-outlined text-slate-300 text-5xl">person</span>
    <p class="text-sm font-semibold text-slate-400 mt-3">Aucun enseignant actif enregistré.</p>
</div>
@else

{{-- Liste enseignants --}}
<div class="space-y-4">
    @foreach($enseignants as $enseignant)
    @php $actif = $enseignant->user->est_actif; @endphp

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- En-tête enseignant --}}
        <div class="p-4 md:p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-11 h-11 rounded-full {{ $actif ? 'bg-primary/10' : 'bg-slate-100' }} flex items-center justify-center shrink-0">
                    <span class="text-sm font-bold {{ $actif ? 'text-primary' : 'text-slate-400' }}">
                        {{ strtoupper(substr($enseignant->user->prenom, 0, 1) . substr($enseignant->user->nom, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <p class="text-base font-bold text-navy-900">{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</p>
                    <p class="text-xs text-navy-700">Spécialité : {{ $enseignant->specialite ?? '—' }}</p>
                </div>
            </div>
            @if($anneeSelectionnee->estActive())
            <button onclick="openModal('modal-assign-{{ $enseignant->id }}')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
                <span class="material-symbols-outlined text-base">add</span>
                Assigner
            </button>
            @endif
        </div>

        {{-- Attributions --}}
        @if($enseignant->attributions->isEmpty())
        <div class="border-t border-slate-100 px-5 py-4">
            <p class="text-xs text-slate-400 italic">Aucune attribution pour cette année.</p>
        </div>
        @else
        <div class="border-t border-slate-100 divide-y divide-slate-100">
            @foreach($enseignant->attributions as $attribution)
            <div class="px-5 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-wrap">
                    @if($attribution->matiere->est_scientifique)
                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg">
                        {{ $attribution->matiere->nom }}
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 text-violet-700 text-xs font-bold rounded-lg">
                        {{ $attribution->matiere->nom }}
                    </span>
                    @endif
                    <span class="flex items-center gap-1 text-sm text-navy-700">
                        <span class="material-symbols-outlined text-sm text-slate-400">arrow_forward</span>
                        {{ $attribution->classe->nom }}
                    </span>
                    @if($attribution->est_prof_principal)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                        <span class="material-symbols-outlined text-xs">star</span>Prof Principal
                    </span>
                    @endif
                </div>
                @if($anneeSelectionnee->estActive())
                <button onclick="openModal('modal-delete-{{ $attribution->id }}')"
                    class="p-1.5 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors shrink-0">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>

    {{-- Modal Assigner --}}
    @if($anneeSelectionnee->estActive())
    <div id="modal-assign-{{ $enseignant->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-assign-{{ $enseignant->id }}')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-primary">
                            {{ strtoupper(substr($enseignant->user->prenom, 0, 1) . substr($enseignant->user->nom, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-navy-900">Assigner {{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</h3>
                        <p class="text-xs text-navy-700">{{ $enseignant->specialite ?? '—' }}</p>
                    </div>
                </div>
                <button onclick="closeModal('modal-assign-{{ $enseignant->id }}')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.attributions.store') }}" class="p-5 space-y-4">
                @csrf
                <input type="hidden" name="enseignant_id" value="{{ $enseignant->id }}"/>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Matière <span class="text-rose-500">*</span></label>
                    <select name="matiere_id"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">-- Choisir une matière --</option>
                        @foreach($matieres as $matiere)
                        <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Classe <span class="text-rose-500">*</span></label>
                    <select name="classe_id"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">-- Choisir une classe --</option>
                        @foreach($classes as $classe)
                        <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date d'attribution <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_attribution"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
                <div>
                    <label class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
                        <input type="checkbox" name="est_prof_principal" value="1" class="accent-primary w-4 h-4 mt-0.5 shrink-0"/>
                        <div>
                            <p class="text-sm font-semibold text-navy-900">Désigner comme professeur principal</p>
                        </div>
                    </label>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeModal('modal-assign-{{ $enseignant->id }}')" class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                    <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Modals Retirer --}}
    @foreach($enseignant->attributions as $attribution)
    <div id="modal-delete-{{ $attribution->id }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-{{ $attribution->id }}')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-rose-500 text-2xl">link_off</span>
                </div>
                <h3 class="text-base font-bold text-navy-900 mb-2">Retirer cette attribution ?</h3>
                <p class="text-sm text-navy-700 mb-6">
                    <strong>{{ $enseignant->user->prenom }} {{ $enseignant->user->nom }}</strong> ne sera plus assigné à
                    <strong>{{ $attribution->matiere->nom }}</strong> en <strong>{{ $attribution->classe->nom }}</strong>.
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeModal('modal-delete-{{ $attribution->id }}')"
                        class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Annuler</button>
                    <form method="POST" action="{{ route('admin.attributions.destroy', $attribution) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">Retirer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @endforeach
</div>

{{-- Pagination --}}
@if($enseignants->hasPages())
<div class="mt-5 flex items-center justify-between">
    <p class="text-xs text-navy-700">
        Affichage de <strong>{{ $enseignants->firstItem() }}</strong> à <strong>{{ $enseignants->lastItem() }}</strong>
        sur <strong>{{ $enseignants->total() }}</strong> enseignants
    </p>
    <div class="flex items-center gap-1">
        @if($enseignants->onFirstPage())
        <span class="px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">←</span>
        @else
        <a href="{{ $enseignants->previousPageUrl() }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">←</a>
        @endif

        @foreach($enseignants->getUrlRange(max(1, $enseignants->currentPage()-2), min($enseignants->lastPage(), $enseignants->currentPage()+2)) as $page => $url)
        @if($page == $enseignants->currentPage())
        <span class="px-3 py-2 text-xs font-bold text-white bg-primary rounded-xl">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">{{ $page }}</a>
        @endif
        @endforeach

        @if($enseignants->hasMorePages())
        <a href="{{ $enseignants->nextPageUrl() }}" class="px-3 py-2 text-xs font-semibold text-navy-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">→</a>
        @else
        <span class="px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">→</span>
        @endif
    </div>
</div>
@endif

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
        openModal('modal-assign-{{ old("enseignant_id") }}');
    @endif
</script>
@endsection