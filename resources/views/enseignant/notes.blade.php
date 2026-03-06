@extends('layouts.enseignant')

@section('title', 'Saisie des Notes — EduCore')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('enseignant.classes.index') }}" class="text-navy-700 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Saisie des Notes</h1>
            </div>
            <p class="text-sm text-navy-700">
                <span class="font-semibold text-navy-900">{{ $classe->nom }}</span>
                · {{ $matiere->nom }}
                · <span class="font-semibold">Semestre {{ $semestre }}</span>
            </p>
        </div>
        <form method="GET" action="{{ route('enseignant.notes.index') }}" class="flex items-center gap-2 shrink-0">
            <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
            <select name="semestre" onchange="this.form.submit()"
                class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                <option value="1" {{ $semestre == 1 ? 'selected' : '' }}>Semestre 1</option>
                <option value="2" {{ $semestre == 2 ? 'selected' : '' }}>Semestre 2</option>
            </select>
        </form>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-blue-600 text-xl">bar_chart</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Moyenne de Classe</p>
                @if($moyenneClasse !== null)
                <p class="text-2xl font-extrabold text-navy-900">{{ number_format($moyenneClasse, 1) }}<span class="text-sm font-semibold text-slate-400">/20</span></p>
                @else
                <p class="text-sm font-semibold text-slate-400 mt-1">Pas encore calculée</p>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-amber-500 text-xl">task_alt</span>
            </div>
            <div class="flex-1">
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Taux de Saisie</p>
                <p class="text-2xl font-extrabold text-navy-900" id="taux-saisie">{{ $tauxSaisie }}<span class="text-sm font-semibold text-slate-400">%</span>
                    <span class="text-xs font-semibold text-slate-400 ml-1" id="compteur-notes">{{ $notesSaisies }}/{{ $notesAttendues }}</span>
                </p>
                <div class="mt-1.5 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-1.5 bg-amber-400 rounded-full transition-all" id="barre-progression" style="width: {{ $tauxSaisie }}%"></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-rose-500 text-xl">group</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider">Élèves Incomplets</p>
                <p class="text-2xl font-extrabold text-navy-900" id="eleves-incomplets">{{ $elevesIncomplets }}
                    <span class="text-sm font-semibold text-slate-400">à compléter</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Notification AJAX --}}
    <div id="notif" class="hidden p-4 rounded-xl flex items-center gap-3 transition-all"></div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-navy-900">Notes — Semestre {{ $semestre }}</h2>
            <span class="text-xs text-navy-700 font-medium">{{ $inscriptions->count() }} élève{{ $inscriptions->count() > 1 ? 's' : '' }}</span>
        </div>

        @if($inscriptions->isEmpty())
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-5xl">group</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucun élève inscrit dans cette classe.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width: 860px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider w-44">Nom de l'élève</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 1</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 2</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Interro 3</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-indigo-500 uppercase tracking-wider text-center bg-indigo-50/50">Moy. Interro</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Devoir 1</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Devoir 2</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-emerald-600 uppercase tracking-wider text-center bg-emerald-50/50">Moy. Générale</th>
                        <th class="px-2 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="tbody-notes">
                    @foreach($inscriptions as $inscription)
                    @php
                        $eleve   = $inscription->eleve->user;
                        $notes   = $notesParInscription[$inscription->id] ?? [];
                        $types   = ['interrogation1', 'interrogation2', 'interrogation3', 'devoir1', 'devoir2'];
                        $complet = count(array_filter($types, fn($t) => isset($notes[$t]))) === 5;
                        $moyInterroExist = $moyennesParInscription[$inscription->id]['moyenne_interrogations'] ?? null;
                        $moyGenExist     = $moyennesParInscription[$inscription->id]['moyenne_generale'] ?? null;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors" data-inscription="{{ $inscription->id }}">

                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                    <span class="text-[10px] font-bold text-primary">
                                        {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-navy-900 truncate">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                            </div>
                        </td>

                        {{-- 3 Interrogations --}}
                        @foreach(['interrogation1', 'interrogation2', 'interrogation3'] as $type)
                        @php $valeur = $notes[$type] ?? null; @endphp
                        <td class="px-2 py-3 text-center">
                            <div class="relative inline-flex items-center justify-center">
                                <input
                                    type="number"
                                    data-inscription="{{ $inscription->id }}"
                                    data-matiere="{{ $matiere->id }}"
                                    data-semestre="{{ $semestre }}"
                                    data-type="{{ $type }}"
                                    data-classe="{{ $classe->id }}"
                                    value="{{ $valeur !== null ? $valeur + 0 : '' }}"
                                    min="0" max="20" step="0.5"
                                    placeholder="--"
                                    class="note-input w-14 px-2 py-1.5 text-sm font-bold text-center rounded-xl border
                                        {{ $valeur !== null ? 'bg-slate-50 border-slate-200 text-navy-900' : 'bg-white border-dashed border-slate-300 text-slate-400' }}
                                        focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                />
                                <span class="save-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full hidden"></span>
                            </div>
                        </td>
                        @endforeach

                        {{-- Moy. Interro --}}
                        <td class="px-2 py-3 text-center bg-indigo-50/30">
                            <span class="moy-interro text-sm font-extrabold text-indigo-600">
                                {{ $moyInterroExist !== null ? number_format($moyInterroExist, 2) : '—' }}
                            </span>
                        </td>

                        {{-- 2 Devoirs --}}
                        @foreach(['devoir1', 'devoir2'] as $type)
                        @php $valeur = $notes[$type] ?? null; @endphp
                        <td class="px-2 py-3 text-center">
                            <div class="relative inline-flex items-center justify-center">
                                <input
                                    type="number"
                                    data-inscription="{{ $inscription->id }}"
                                    data-matiere="{{ $matiere->id }}"
                                    data-semestre="{{ $semestre }}"
                                    data-type="{{ $type }}"
                                    data-classe="{{ $classe->id }}"
                                    value="{{ $valeur !== null ? $valeur + 0 : '' }}"
                                    min="0" max="20" step="0.5"
                                    placeholder="--"
                                    class="note-input w-14 px-2 py-1.5 text-sm font-bold text-center rounded-xl border
                                        {{ $valeur !== null ? 'bg-slate-50 border-slate-200 text-navy-900' : 'bg-white border-dashed border-slate-300 text-slate-400' }}
                                        focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                />
                                <span class="save-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full hidden"></span>
                            </div>
                        </td>
                        @endforeach

                        {{-- Moy. Générale --}}
                        <td class="px-2 py-3 text-center bg-emerald-50/30">
                            <span class="moy-generale text-sm font-extrabold text-emerald-600">
                                {{ $moyGenExist !== null ? number_format($moyGenExist, 2) : '—' }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td class="px-2 py-3 text-center">
                            <span class="statut-badge inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full uppercase
                                {{ $complet ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $complet ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $complet ? 'Complet' : 'Incomplet' }}
                            </span>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Bouton valider moyennes --}}
        <div class="p-4 md:p-5 border-t border-slate-200 flex items-center justify-between gap-4">
            <p class="text-xs text-slate-400">Les moyennes sont calculées automatiquement. Cliquez sur "Valider" pour les enregistrer officiellement.</p>
            <button id="btn-valider" onclick="validerMoyennes()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="material-symbols-outlined text-base">check_circle</span>
                Valider les moyennes
            </button>
        </div>

        @endif
    </div>

</div>

@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const STORE_URL = '{{ route("enseignant.notes.store") }}';
const VALIDER_URL = '{{ route("enseignant.notes.valider-moyennes") }}';
const NOTES_ATTENDUES = {{ $notesAttendues }};
let notesSaisies = {{ $notesSaisies }};

// ── Sauvegarde AJAX d'une note ─────────────────────────────────
document.querySelectorAll('.note-input').forEach(input => {
    input.addEventListener('change', function() {
        const val = parseFloat(this.value);
        if (isNaN(val) || val < 0 || val > 20) return;

        const indicator = this.parentElement.querySelector('.save-indicator');
        indicator.className = 'save-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full bg-amber-400';

        fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                inscription_id:  this.dataset.inscription,
                matiere_id:      this.dataset.matiere,
                numero_semestre: this.dataset.semestre,
                type:            this.dataset.type,
                valeur:          val,
                classe_id:       this.dataset.classe,
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Indicateur vert
                indicator.className = 'save-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full bg-emerald-500';
                this.classList.remove('border-dashed', 'border-slate-300', 'text-slate-400');
                this.classList.add('bg-slate-50', 'border-slate-200', 'text-navy-900');

                if (data.estNouvelle) notesSaisies++;
                mettreAJourKPIs();
                calculerMoyennesLigne(this.closest('tr'));
            } else {
                indicator.className = 'save-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full bg-rose-500';
            }
            setTimeout(() => indicator.classList.add('hidden'), 2000);
        })
        .catch(() => {
            indicator.className = 'save-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full bg-rose-500';
            setTimeout(() => indicator.classList.add('hidden'), 2000);
        });
    });
});

// ── Calcul temps réel des moyennes d'une ligne ─────────────────
function calculerMoyennesLigne(row) {
    const inputs = row.querySelectorAll('.note-input');
    const vals = {};
    inputs.forEach(i => {
        if (i.value !== '') vals[i.dataset.type] = parseFloat(i.value);
    });

    // Moy. Interro (dès que les 3 interros sont renseignées)
    const moyInterroEl = row.querySelector('.moy-interro');
    if (vals.interrogation1 !== undefined && vals.interrogation2 !== undefined && vals.interrogation3 !== undefined) {
        const moyInterro = (vals.interrogation1 + vals.interrogation2 + vals.interrogation3) / 3;
        moyInterroEl.textContent = moyInterro.toFixed(2);
        moyInterroEl.classList.add('text-indigo-600');

        // Moy. Générale (dès que les 5 notes sont renseignées)
        const moyGenEl = row.querySelector('.moy-generale');
        if (vals.devoir1 !== undefined && vals.devoir2 !== undefined) {
            const moyGen = (moyInterro + vals.devoir1 + vals.devoir2) / 3;
            moyGenEl.textContent = moyGen.toFixed(2);
            moyGenEl.classList.add('text-emerald-600');

            // Statut complet
            const badge = row.querySelector('.statut-badge');
            badge.className = 'statut-badge inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold rounded-full uppercase bg-emerald-100 text-emerald-700';
            badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Complet';
        }
    }
}

// ── Mise à jour KPIs ───────────────────────────────────────────
function mettreAJourKPIs() {
    const taux = NOTES_ATTENDUES > 0 ? Math.round((notesSaisies / NOTES_ATTENDUES) * 100) : 0;
    document.getElementById('taux-saisie').childNodes[0].textContent = taux;
    document.getElementById('compteur-notes').textContent = notesSaisies + '/' + NOTES_ATTENDUES;
    document.getElementById('barre-progression').style.width = taux + '%';

    // Élèves incomplets
    let incomplets = 0;
    document.querySelectorAll('#tbody-notes tr').forEach(row => {
        const inputs = row.querySelectorAll('.note-input');
        const remplis = Array.from(inputs).filter(i => i.value !== '').length;
        if (remplis < 5) incomplets++;
    });
    document.getElementById('eleves-incomplets').childNodes[0].textContent = incomplets;
}

// ── Valider les moyennes en base ───────────────────────────────
function validerMoyennes() {
    const btn = document.getElementById('btn-valider');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-base animate-spin">refresh</span> Enregistrement...';

    const moyennes = [];
    document.querySelectorAll('#tbody-notes tr').forEach(row => {
        const inscriptionId = row.dataset.inscription;
        const moyGen = row.querySelector('.moy-generale').textContent.trim();
        const moyInterro = row.querySelector('.moy-interro').textContent.trim();
        if (moyGen !== '—' && moyGen !== '') {
            moyennes.push({
                inscription_id:        inscriptionId,
                moyenne_interrogations: parseFloat(moyInterro),
                moyenne_generale:       parseFloat(moyGen),
            });
        }
    });

    if (moyennes.length === 0) {
        afficherNotif('Aucune moyenne à valider — complétez d\'abord toutes les notes.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span> Valider les moyennes';
        return;
    }

    fetch(VALIDER_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            matiere_id: document.querySelector('.note-input').dataset.matiere,
            semestre:   document.querySelector('.note-input').dataset.semestre,
            moyennes:   moyennes,
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            afficherNotif('Moyennes validées et enregistrées avec succès.', 'success');
            btn.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span> Moyennes validées';
        } else {
            afficherNotif('Erreur lors de l\'enregistrement.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-base">check_circle</span> Valider les moyennes';
        }
    });
}

// ── Notification ───────────────────────────────────────────────
function afficherNotif(msg, type) {
    const notif = document.getElementById('notif');
    notif.className = `p-4 rounded-xl flex items-center gap-3 ${type === 'success' ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200'}`;
    notif.innerHTML = `<span class="material-symbols-outlined ${type === 'success' ? 'text-emerald-500' : 'text-rose-500'}">${type === 'success' ? 'check_circle' : 'error'}</span>
        <p class="text-sm font-semibold ${type === 'success' ? 'text-emerald-700' : 'text-rose-700'}">${msg}</p>`;
    notif.classList.remove('hidden');
    setTimeout(() => notif.classList.add('hidden'), 4000);
}

// Calcul initial pour les lignes déjà remplies
document.querySelectorAll('#tbody-notes tr').forEach(row => calculerMoyennesLigne(row));
</script>
@endsection