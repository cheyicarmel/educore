@extends('layouts.admin')

@section('title', 'Années Académiques — EduCore')

@section('content')

{{-- Header de page --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Années Académiques</h1>
        <p class="text-sm text-navy-700 mt-1">Gérez les années académiques de l'établissement.</p>
    </div>
    <button onclick="openModal('modal-create')"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-colors shrink-0">
        <span class="material-symbols-outlined text-base">add</span>
        Nouvelle Année
    </button>
</div>

{{-- Message de succès --}}
@if(session('success'))
<div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
    <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
</div>
@endif

{{-- Message d'erreur --}}
@if(session('error'))
<div class="mb-5 p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3">
    <span class="material-symbols-outlined text-rose-500">error</span>
    <p class="text-sm font-semibold text-rose-700">{{ session('error') }}</p>
</div>
@endif

{{-- Tableau des années académiques --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 md:p-5 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-base font-bold text-navy-900">Liste des Années Académiques</h2>
        <span class="text-xs text-navy-700 font-medium">3 années</span>
    </div>

    {{-- Vue tableau sur md+ --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-left" style="min-width:600px;">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Année</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Date de début</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Date de fin</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                {{-- Ligne active --}}
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-navy-900">2024 - 2025</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">01 Sep. 2024</td>
                    <td class="px-6 py-4 text-sm text-navy-700">30 Juin 2025</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-1')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-1')"
                                class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                {{-- Ligne inactive --}}
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-navy-900">2023 - 2024</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">01 Sep. 2023</td>
                    <td class="px-6 py-4 text-sm text-navy-700">30 Juin 2024</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Terminée
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-2')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-2')"
                                class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                {{-- Ligne inactive --}}
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-navy-900">2022 - 2023</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-navy-700">01 Sep. 2022</td>
                    <td class="px-6 py-4 text-sm text-navy-700">30 Juin 2023</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-full uppercase">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            Terminée
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openModal('modal-edit-3')"
                                class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Modifier">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="openModal('modal-delete-3')"
                                class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors"
                                title="Supprimer">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Vue cartes sur mobile --}}
    <div class="md:hidden divide-y divide-slate-100">
        {{-- Carte 1 --}}
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">2024 - 2025</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-navy-700">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    01 Sep. 2024
                </span>
                <span>→</span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">event</span>
                    30 Juin 2025
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-1')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </button>
                <button onclick="openModal('modal-delete-1')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Supprimer
                </button>
            </div>
        </div>
        {{-- Carte 2 --}}
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">2023 - 2024</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Terminée
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-navy-700">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    01 Sep. 2023
                </span>
                <span>→</span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">event</span>
                    30 Juin 2024
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-2')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </button>
                <button onclick="openModal('modal-delete-2')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Supprimer
                </button>
            </div>
        </div>
        {{-- Carte 3 --}}
        <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-navy-900">2022 - 2023</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    Terminée
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-navy-700">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    01 Sep. 2022
                </span>
                <span>→</span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">event</span>
                    30 Juin 2023
                </span>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button onclick="openModal('modal-edit-3')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Modifier
                </button>
                <button onclick="openModal('modal-delete-3')"
                    class="flex-1 flex items-center justify-center gap-1.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════ MODAL CRÉER ═══════════════ --}}
<div id="modal-create" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-create')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Nouvelle Année Académique</h3>
            <button onclick="closeModal('modal-create')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="#" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Libellé <span class="text-rose-500">*</span></label>
                <input type="text" name="libelle" placeholder="Ex: 2025 - 2026"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date de début <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_debut"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date de fin <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_fin"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Annuler
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════ MODAL MODIFIER ═══════════════ --}}
<div id="modal-edit-1" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit-1')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-navy-900">Modifier l'Année Académique</h3>
            <button onclick="closeModal('modal-edit-1')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <form method="POST" action="#" class="p-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-navy-900 mb-1.5">Libellé <span class="text-rose-500">*</span></label>
                <input type="text" name="libelle" value="2024 - 2025"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date de début <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_debut" value="2024-09-01"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5">Date de fin <span class="text-rose-500">*</span></label>
                    <input type="date" name="date_fin" value="2025-06-30"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-navy-900 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"/>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-edit-1')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Annuler
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════ MODAL SUPPRIMER ═══════════════ --}}
<div id="modal-delete-1" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-delete-1')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-rose-500 text-2xl">delete</span>
            </div>
            <h3 class="text-base font-bold text-navy-900 mb-2">Supprimer cette année ?</h3>
            <p class="text-sm text-navy-700 mb-6">L'année <strong>2024 - 2025</strong> sera définitivement supprimée. Cette action est irréversible.</p>
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeModal('modal-delete-1')"
                    class="flex-1 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Annuler
                </button>
                <form method="POST" action="#" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full py-2.5 text-sm font-bold text-white bg-rose-500 hover:bg-rose-600 rounded-xl transition-colors">
                        Supprimer
                    </button>
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

    // Fermer avec la touche Escape
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