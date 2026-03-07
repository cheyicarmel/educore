<div class="bg-white p-4 md:p-6 rounded-2xl border border-slate-200 shadow-sm">
    <h2 class="text-base md:text-lg font-bold text-navy-900 mb-5">Suivi Financier</h2>

    <div class="mb-5">
        <div class="flex justify-between mb-1.5">
            <span class="text-xs font-semibold text-navy-700">Paiement effectué</span>
            <span class="text-xs font-bold text-navy-900">{{ $tauxPaiement }}%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2.5">
            <div class="bg-primary h-2.5 rounded-full transition-all" style="width:{{ $tauxPaiement }}%"></div>
        </div>
        <div class="flex justify-between mt-1.5">
            <span class="text-[10px] text-navy-700">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA payés</span>
            <span class="text-[10px] text-navy-700">{{ number_format($totalDu, 0, ',', ' ') }} FCFA total</span>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between items-center py-2 border-b border-slate-100">
            <span class="text-xs text-navy-700">Total dû</span>
            <span class="text-xs font-bold text-navy-900">{{ number_format($totalDu, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="flex justify-between items-center py-2 border-b border-slate-100">
            <span class="text-xs text-navy-700">Total payé</span>
            <span class="text-xs font-bold text-emerald-600">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="flex justify-between items-center py-2">
            <span class="text-xs text-navy-700">Solde restant</span>
            <span class="text-xs font-bold {{ $soldeRestant > 0 ? 'text-orange-500' : 'text-emerald-600' }}">
                {{ $soldeRestant > 0 ? number_format($soldeRestant, 0, ',', ' ') . ' FCFA' : 'Soldé' }}
            </span>
        </div>
    </div>

    @if($soldeRestant > 0)
    <div class="mt-4 p-3 bg-orange-50 rounded-xl border border-orange-100">
        <p class="text-xs font-semibold text-orange-700 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">info</span>
            Solde restant à régler
        </p>
    </div>
    @else
    <div class="mt-4 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
        <p class="text-xs font-semibold text-emerald-700 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">check_circle</span>
            Scolarité entièrement réglée
        </p>
    </div>
    @endif
</div>