@extends('layouts.admin')

@section('title', 'Bulletins — EduCore')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight">Bulletins</h1>
        <p class="text-sm text-navy-700 mt-1">Publiez les bulletins par classe — les élèves pourront ensuite les télécharger.</p>
    </div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-500">check_circle</span>
        <p class="text-sm font-semibold text-emerald-700">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-rose-500">error</span>
        <p class="text-sm font-semibold text-rose-700">{{ session('error') }}</p>
    </div>
    @endif

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-200">
            <h2 class="text-base font-bold text-navy-900">Classes — Année {{ $anneeActive?->libelle }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="min-width: 700px;">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider">Classe</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Série</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Bulletins S1</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Bulletins S2</th>
                        <th class="px-4 py-3 text-[11px] font-bold text-navy-700 uppercase tracking-wider text-center">Bulletins Annuels</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $classe)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-navy-900">{{ $classe->nom }}</p>
                            <p class="text-xs text-slate-400">{{ ucfirst($classe->cycle) }} cycle</p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-semibold text-navy-700">{{ $classe->serie->libelle ?? '—' }}</span>
                        </td>

                        {{-- S1 --}}
                        <td class="px-4 py-4 text-center">
                            @if($classe->bulletins_publies_s1)
                            <div class="flex flex-col items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Publié
                                </span>
                                <form method="POST" action="{{ route('admin.bulletins.depublier') }}">
                                    @csrf
                                    <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                    <input type="hidden" name="semestre" value="1"/>
                                    <button type="submit" class="text-[10px] font-bold text-rose-500 hover:underline">Dépublier</button>
                                </form>
                            </div>
                            @else
                            <form method="POST" action="{{ route('admin.bulletins.publier') }}">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                <input type="hidden" name="semestre" value="1"/>
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-[11px] font-bold rounded-lg hover:bg-primary/90 transition-colors">
                                    <span class="material-symbols-outlined text-xs">publish</span>
                                    Publier S1
                                </button>
                            </form>
                            @endif
                        </td>

                        {{-- S2 --}}
                        <td class="px-4 py-4 text-center">
                            @if($classe->bulletins_publies_s2)
                            <div class="flex flex-col items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Publié
                                </span>
                                <form method="POST" action="{{ route('admin.bulletins.depublier') }}">
                                    @csrf
                                    <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                    <input type="hidden" name="semestre" value="2"/>
                                    <button type="submit" class="text-[10px] font-bold text-rose-500 hover:underline">Dépublier</button>
                                </form>
                            </div>
                            @else
                            <form method="POST" action="{{ route('admin.bulletins.publier') }}">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                <input type="hidden" name="semestre" value="2"/>
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary text-white text-[11px] font-bold rounded-lg hover:bg-primary/90 transition-colors">
                                    <span class="material-symbols-outlined text-xs">publish</span>
                                    Publier S2
                                </button>
                            </form>
                            @endif
                        </td>

                        {{-- Annuel --}}
                        <td class="px-4 py-4 text-center">
                            @if($classe->bulletins_publies_annuel)
                            <div class="flex flex-col items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Publié
                                </span>
                                <form method="POST" action="{{ route('admin.bulletins.depublier') }}">
                                    @csrf
                                    <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                    <input type="hidden" name="semestre" value="annuel"/>
                                    <button type="submit" class="text-[10px] font-bold text-rose-500 hover:underline">Dépublier</button>
                                </form>
                            </div>
                            @else
                            <form method="POST" action="{{ route('admin.bulletins.publier') }}">
                                @csrf
                                <input type="hidden" name="classe_id" value="{{ $classe->id }}"/>
                                <input type="hidden" name="semestre" value="annuel"/>
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-600 text-white text-[11px] font-bold rounded-lg hover:bg-violet-700 transition-colors">
                                    <span class="material-symbols-outlined text-xs">publish</span>
                                    Publier Annuel
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-400 font-semibold">Aucune classe trouvée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection