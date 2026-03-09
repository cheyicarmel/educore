@extends('layouts.comptable')

@section('title', 'Enregistrer un Paiement — EduCore')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Enregistrer un Paiement</h1>
        <p class="text-sm text-navy-700 mt-1">Recherchez un élève puis saisissez les informations du paiement.</p>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
            </div>
            @if(session('paiement_id'))
                <a href="{{ route('comptable.paiements.recu', session('paiement_id')) }}"
                    target="_blank"
                    class="shrink-0 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                    Voir le reçu
                </a>
            @endif
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3">
            <span class="material-symbols-outlined text-rose-500">error</span>
            <p class="text-sm font-semibold text-rose-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Barre de recherche --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 class="text-sm font-bold text-navy-900 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-base">search</span>
            Rechercher un élève
        </h2>
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">person_search</span>
            <input type="text" id="search-input" placeholder="Tapez le nom ou prénom de l'élève..."
                autocomplete="off"
                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            {{-- Dropdown résultats --}}
            <div id="search-dropdown"
                class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 hidden overflow-hidden">
            </div>
        </div>
        <p class="text-xs text-slate-400 mt-2">Minimum 2 caractères pour lancer la recherche.</p>
    </div>

    {{-- Fiche élève + Formulaire --}}
    @if($eleve && $inscription && $suivi)
        <div class="space-y-5" id="fiche-section">

            {{-- Fiche élève --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">person</span>
                    <h2 class="text-sm font-bold text-navy-900">Informations de l'élève</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-4 mb-5">
                        <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="text-xl font-extrabold text-primary">
                                {{ strtoupper(substr($eleve->user->prenom, 0, 1) . substr($eleve->user->nom, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-base font-extrabold text-navy-900">{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</p>
                            <p class="text-sm text-navy-700">{{ $inscription->classe->nom }}
                                @if($inscription->classe->serie)
                                · Série {{ $inscription->classe->serie->libelle }}
                                @endif
                            </p>
                            @if($eleve->matricule)
                            <p class="text-xs text-slate-400 mt-0.5">Matricule : {{ $eleve->matricule }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Données financières --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3 bg-slate-50 rounded-xl text-center">
                            <p class="text-[10px] font-bold text-navy-700 uppercase tracking-wider mb-1">Total Dû</p>
                            <p class="text-sm font-extrabold text-navy-900">{{ number_format($suivi->total_du, 0, ',', ' ') }}</p>
                            <p class="text-[10px] text-slate-400">FCFA</p>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-xl text-center">
                            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider mb-1">Déjà Payé</p>
                            <p class="text-sm font-extrabold text-emerald-700">{{ number_format($suivi->total_paye, 0, ',', ' ') }}</p>
                            <p class="text-[10px] text-emerald-600">FCFA</p>
                        </div>
                        <div class="p-3 {{ $suivi->solde_restant > 0 ? 'bg-rose-50' : 'bg-emerald-50' }} rounded-xl text-center">
                            <p class="text-[10px] font-bold {{ $suivi->solde_restant > 0 ? 'text-rose-700' : 'text-emerald-700' }} uppercase tracking-wider mb-1">Solde Restant</p>
                            <p class="text-sm font-extrabold {{ $suivi->solde_restant > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($suivi->solde_restant, 0, ',', ' ') }}</p>
                            <p class="text-[10px] {{ $suivi->solde_restant > 0 ? 'text-rose-400' : 'text-emerald-400' }}">FCFA</p>
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-navy-700 font-medium">Statut :</span>
                        @php
                            $statutConfig = match($suivi->statut) {
                                'solde'     => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Soldé'],
                                default     => ['bg' => 'bg-rose-100',    'text' => 'text-rose-700',    'dot' => 'bg-rose-500',    'label' => 'En retard'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 {{ $statutConfig['bg'] }} {{ $statutConfig['text'] }} text-[10px] font-bold rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statutConfig['dot'] }}"></span>
                            {{ $statutConfig['label'] }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Formulaire paiement --}}
            @if($suivi->solde_restant > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">add_card</span>
                    <h2 class="text-sm font-bold text-navy-900">Saisie du Paiement</h2>
                </div>
                <form method="POST" action="{{ route('comptable.paiements.store') }}" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="inscription_id" value="{{ $inscription->id }}"/>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Montant --}}
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">
                                Montant <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="montant" min="1" max="{{ $suivi->solde_restant }}"
                                    placeholder="Ex: 50000"
                                    class="w-full px-4 py-2.5 pr-16 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                    required/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">FCFA</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Max : {{ number_format($suivi->solde_restant, 0, ',', ' ') }} FCFA</p>
                        </div>

                        {{-- Mode de paiement --}}
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">
                                Mode de Paiement <span class="text-rose-500">*</span>
                            </label>
                            <select name="mode_paiement"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                required>
                                <option value="">Choisir...</option>
                                <option value="especes">Espèces</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="virement">Virement bancaire</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div>
                            <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">
                                Date du Paiement <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                                required/>
                        </div>
                    </div>

                    @if($errors->any())
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl">
                        @foreach($errors->all() as $error)
                        <p class="text-xs text-rose-600 font-semibold">{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    <div class="flex items-center gap-3 pt-2">
                        <a href="{{ route('comptable.paiements.create') }}"
                            class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors text-center">
                            Annuler
                        </a>
                        <button type="submit"
                            class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-base">save</span>
                            Enregistrer le Paiement
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center">
                <span class="material-symbols-outlined text-emerald-400 text-4xl">check_circle</span>
                <p class="text-sm font-bold text-emerald-700 mt-2">Cet élève est à jour dans ses paiements.</p>
                <p class="text-xs text-emerald-600 mt-1">Aucun solde restant à régler.</p>
            </div>
            @endif
        </div>
    @endif

</div>
@endsection

@section('scripts')
    <script>
        const searchInput    = document.getElementById('search-input');
        const searchDropdown = document.getElementById('search-dropdown');
        let searchTimeout    = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) { searchDropdown.classList.add('hidden'); return; }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route('comptable.paiements.search') }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) {
                            searchDropdown.innerHTML = `
                                <div class="p-4 text-center text-sm text-slate-400 font-semibold">Aucun élève trouvé</div>`;
                            searchDropdown.classList.remove('hidden');
                            return;
                        }
                        searchDropdown.innerHTML = data.map(e => `
                            <a href="?eleve_id=${e.eleve_id}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-0">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-[11px] font-bold text-primary shrink-0">
                                    ${e.nom.split(' ').map(n => n[0]).slice(0,2).join('').toUpperCase()}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-navy-900">${e.nom}</p>
                                    <p class="text-xs text-navy-700">${e.classe}</p>
                                </div>
                            </a>`).join('');
                        searchDropdown.classList.remove('hidden');
                    });
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });
    </script>
@endsection