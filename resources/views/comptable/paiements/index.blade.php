@extends('layouts.comptable')

@section('title', 'Historique des Paiements — EduCore')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Historique des Paiements</h1>
            <p class="text-sm text-navy-700 mt-1">
                Année <span class="font-semibold">{{ $anneeActive?->libelle }}</span>
                · <span class="font-semibold">{{ $nombreTotal }}</span> paiement{{ $nombreTotal > 1 ? 's' : '' }} enregistré{{ $nombreTotal > 1 ? 's' : '' }}
            </p>
        </div>
        <a href="{{ route('comptable.paiements.create') }}"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>
            Nouveau Paiement
        </a>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 gap-3 md:gap-4">
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Total Encaissé (Année)</p>
            <h3 class="text-base font-extrabold text-navy-900 mt-0.5">
                {{ number_format($totalGeneral, 0, ',', ' ') }} <span class="text-xs font-semibold text-navy-700">FCFA</span>
            </h3>
        </div>
        <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg w-fit mb-3">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
            </div>
            <p class="text-navy-700 text-xs font-medium">Nombre de Paiements</p>
            <h3 class="text-2xl font-extrabold text-navy-900 mt-0.5">{{ $nombreTotal }}</h3>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 md:p-5">
        <form method="GET" action="{{ route('comptable.historique') }}">
            <div class="flex flex-col sm:flex-row items-end gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Rechercher</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nom de l'élève..."
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                    </div>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-navy-700 uppercase tracking-wider mb-1.5">Classe</label>
                    <select name="classe"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all">
                        <option value="">Toutes les classes</option>
                        @foreach($classes as $classe)
                        <option value="{{ $classe->id }}" {{ request('classe') == $classe->id ? 'selected' : '' }}>
                            {{ $classe->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-base">filter_alt</span>
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'classe']))
                    <a href="{{ route('comptable.historique') }}"
                        class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-colors" title="Réinitialiser">
                        <span class="material-symbols-outlined text-base">close</span>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-navy-900">Liste des Paiements</h2>
            <span class="text-xs text-navy-700 font-medium">{{ $paiements->total() }} résultat{{ $paiements->total() > 1 ? 's' : '' }}</span>
        </div>

        @if($paiements->isEmpty())
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-slate-300 text-5xl">receipt_long</span>
            <p class="text-sm font-semibold text-slate-400 mt-3">Aucun paiement trouvé.</p>
        </div>
        @else

        {{-- Vue tableau md+ --}}
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left" style="min-width:700px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Élève</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Référence</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Mode</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Montant</th>
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Reçu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($paiements as $p)
                    @php $eleve = $p->inscription->eleve->user; @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">
                                    {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                                </div>
                                <span class="text-sm font-semibold text-navy-900 whitespace-nowrap">{{ $eleve->prenom }} {{ $eleve->nom }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-sm text-navy-700 whitespace-nowrap">{{ $p->inscription->classe->nom }}</td>
                        <td class="px-5 py-3">
                            <span class="text-xs font-bold text-primary">{{ $p->reference ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-navy-700 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2.5 py-1 text-[10px] font-bold rounded-lg
                                {{ $p->mode_paiement === 'especes'      ? 'bg-emerald-100 text-emerald-700' :
                                  ($p->mode_paiement === 'mobile_money' ? 'bg-blue-100 text-blue-700' :
                                  ($p->mode_paiement === 'virement'     ? 'bg-violet-100 text-violet-700' :
                                                                          'bg-amber-100 text-amber-700')) }}">
                                {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm font-extrabold text-navy-900 text-right whitespace-nowrap">
                            {{ number_format($p->montant, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('comptable.paiements.recu', $p->id) }}" target="_blank"
                                class="inline-flex items-center justify-center w-8 h-8 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg transition-colors"
                                title="Voir le reçu PDF">
                                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Vue cartes mobile --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($paiements as $p)
            @php $eleve = $p->inscription->eleve->user; @endphp
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary shrink-0">
                            {{ strtoupper(substr($eleve->prenom, 0, 1) . substr($eleve->nom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-navy-900">{{ $eleve->prenom }} {{ $eleve->nom }}</p>
                            <p class="text-xs text-navy-700">{{ $p->inscription->classe->nom }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-extrabold text-navy-900">{{ number_format($p->montant, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-navy-700">{{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }}</span>
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-lg bg-slate-100 text-slate-600">
                            {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                        </span>
                    </div>
                    <a href="{{ route('comptable.paiements.recu', $p->id) }}" target="_blank"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 text-rose-600 text-xs font-bold rounded-lg hover:bg-rose-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                        Reçu
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($paiements->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $paiements->links() }}
        </div>
        @endif

        @endif
    </div>

</div>
@endsection