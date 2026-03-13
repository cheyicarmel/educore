<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.5px; color: #1a1a2e; }
        .page { padding: 18px 22px; }

        .header-table { width: 100%; border-bottom: 3px solid #2b6cee; padding-bottom: 10px; margin-bottom: 12px; }
        .school-name { font-size: 14px; font-weight: 800; color: #2b6cee; }
        .school-sub { font-size: 7.5px; color: #4c669a; margin-top: 2px; }
        .doc-title { font-size: 13px; font-weight: 800; color: #0d121b; text-transform: uppercase; text-align: right; }
        .doc-sub { font-size: 8px; color: #4c669a; text-align: right; margin-top: 3px; }

        .eleve-band { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #2b6cee; border-radius: 6px; margin-bottom: 12px; }
        .eleve-band td { padding: 7px 12px; }
        .info-label { color: #4c669a; font-weight: 700; text-transform: uppercase; font-size: 7px; display: block; }
        .info-value { color: #0d121b; font-weight: 800; font-size: 10px; }

        /* Tableau notes enrichi */
        .notes-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .notes-table thead tr.header-main { background: #1e40af; }
        .notes-table thead tr.header-sub { background: #1e3a8a; }
        .notes-table thead th { padding: 5px 4px; font-size: 7.5px; font-weight: 700; color: white; text-align: center; border: 1px solid #1d4ed8; }
        .notes-table thead th.left { text-align: left; padding-left: 8px; }
        .notes-table tbody tr:nth-child(even) { background: #f8fafc; }
        .notes-table tbody tr:nth-child(odd) { background: #ffffff; }
        .notes-table tbody td { padding: 5px 4px; font-size: 8px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; }
        .notes-table tbody td.left { text-align: left; padding-left: 8px; font-weight: 700; }
        .notes-table tbody td.moy-interro { background: #fef9c3; font-weight: 700; }
        .notes-table tbody td.moy-gen { background: #eff6ff; font-weight: 800; border-left: 2px solid #93c5fd; border-right: 2px solid #93c5fd; }
        .notes-table tbody td.moy-coef { background: #f0fdf4; font-weight: 700; }
        .notes-table tfoot td { padding: 6px 4px; font-size: 8.5px; font-weight: 800; background: #1e3a8a; color: white; border: 1px solid #1d4ed8; text-align: center; }
        .notes-table tfoot td.left { text-align: left; padding-left: 8px; }

        .ok { color: #15803d; font-weight: 800; }
        .ko { color: #dc2626; font-weight: 800; }

        /* Résumé */
        .resume-table { width: 100%; border-collapse: separate; border-spacing: 5px; margin-bottom: 10px; }
        .resume-cell { padding: 8px; border-radius: 6px; text-align: center; }
        .resume-label { font-size: 7px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .resume-value { font-size: 15px; font-weight: 800; display: block; }

        .mention-badge { font-size: 8.5px; font-weight: 800; padding: 2px 8px; border-radius: 10px; }
        .m-exc  { background: #fef9c3; color: #a16207; }
        .m-tbi  { background: #f5f3ff; color: #6d28d9; }
        .m-bi   { background: #dcfce7; color: #15803d; }
        .m-ab   { background: #dbeafe; color: #1d4ed8; }
        .m-pa   { background: #fef3c7; color: #d97706; }
        .m-ins  { background: #fee2e2; color: #b91c1c; }

        .sig-table { width: 100%; margin-top: 12px; }
        .sig-table td { width: 33%; padding: 0 5px; vertical-align: top; }
        .sig-box { border: 1px dashed #cbd5e1; border-radius: 4px; padding: 6px 10px; height: 55px; }
        .sig-label { font-size: 7px; font-weight: 700; color: #4c669a; text-transform: uppercase; }
        .footer-line { margin-top: 8px; padding-top: 5px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 7px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="page">

    {{-- EN-TÊTE --}}
    <table class="header-table">
        <tr>
            <td style="width:60%; vertical-align:top;">
                @if($parametres->logo)
                <img src="{{ public_path('storage/' . $parametres->logo) }}" style="height:36px; margin-bottom:4px;"/>
                @endif
                <div class="school-name">{{ $parametres->nom_etablissement }}</div>
                @if($parametres->slogan)<div class="school-sub"><em>{{ $parametres->slogan }}</em></div>@endif
                <div class="school-sub">{{ $parametres->adresse ?? '' }} — {{ $parametres->ville }}, {{ $parametres->pays }}</div>
                @if($parametres->telephone)
                <div class="school-sub">Tél : {{ $parametres->telephone }}{{ $parametres->telephone2 ? ' / ' . $parametres->telephone2 : '' }}</div>
                @endif
            </td>
            <td style="width:40%; vertical-align:top;">
                <div class="doc-title">Bulletin de Notes</div>
                <div class="doc-sub">Semestre {{ $semestre }} — Année {{ $anneeActive?->libelle }}</div>
                <div class="doc-sub">Édité le {{ now()->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- INFOS ÉLÈVE --}}
    <table class="eleve-band">
        <tr>
            <td>
                <span class="info-label">Élève</span>
                <span class="info-value">{{ strtoupper($eleve->nom) }} {{ $eleve->prenom }}</span>
            </td>
            <td>
                <span class="info-label">Classe</span>
                <span class="info-value">{{ $classe->nom }}</span>
            </td>
            <td>
                <span class="info-label">Série</span>
                <span class="info-value">{{ $classe->serie->libelle ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">Année scolaire</span>
                <span class="info-value">{{ $anneeActive?->libelle }}</span>
            </td>
            <td>
                <span class="info-label">Effectif</span>
                <span class="info-value">{{ $effectif }} élèves</span>
            </td>
        </tr>
    </table>

    {{-- TABLEAU NOTES --}}
    <table class="notes-table">
        <thead>
            <tr class="header-main">
                <th class="left" rowspan="2" style="width:18%;">Matière</th>
                @if(!$estPremierCycle)
                <th rowspan="2" style="width:5%;">Coef</th>
                @endif
                <th colspan="3" style="width:22%;">Interrogations</th>
                <th colspan="2" style="width:14%;">Devoirs</th>
                <th rowspan="2" style="width:9%;">Moy. Interro</th>
                <th rowspan="2" style="width:9%;">Moy. Matière</th>
                @if(!$estPremierCycle)
                <th rowspan="2" style="width:9%;">Moy. × Coef</th>
                @endif
                <th rowspan="2" style="width:10%;">Appréciation</th>
            </tr>
            <tr class="header-sub">
                <th style="width:7%;">I1</th>
                <th style="width:7%;">I2</th>
                <th style="width:7%;">I3</th>
                <th style="width:7%;">D1</th>
                <th style="width:7%;">D2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detailMatieres as $dm)
            <tr>
                <td class="left">{{ $dm['matiere'] }}</td>
                @if(!$estPremierCycle)
                    <td>{{ $dm['coefficient'] ?? '—' }}</td>
                @endif
                {{-- Interrogations --}}
                @foreach(['interro1','interro2','interro3'] as $key)
                    <td>
                        @if($dm[$key] !== null)
                            <span class="{{ $dm[$key] >= 10 ? 'ok' : 'ko' }}">{{ number_format($dm[$key], 2) }}</span>
                        @else 
                            —
                        @endif
                    </td>
                @endforeach
                {{-- Devoirs --}}
                @foreach(['devoir1','devoir2'] as $key)
                    <td>
                        @if($dm[$key] !== null)
                            <span class="{{ $dm[$key] >= 10 ? 'ok' : 'ko' }}">{{ number_format($dm[$key], 2) }}</span>
                        @else 
                            —
                        @endif
                    </td>
                @endforeach
                {{-- Moy interro --}}
                <td class="moy-interro">
                    @if($dm['moyenne_interrogations'] !== null)
                        <span class="{{ $dm['moyenne_interrogations'] >= 10 ? 'ok' : 'ko' }}">{{ number_format($dm['moyenne_interrogations'], 2) }}</span>
                    @else 
                        —
                    @endif
                </td>
                {{-- Moy générale --}}
                <td class="moy-gen">
                    @if($dm['moyenne_generale'] !== null)
                        <span class="{{ $dm['moyenne_generale'] >= 10 ? 'ok' : 'ko' }}">{{ number_format($dm['moyenne_generale'], 2) }}</span>
                    @else 
                        —
                    @endif
                </td>
                {{-- Moy × coef --}}
                @if(!$estPremierCycle)
                    <td class="moy-coef">
                        @if($dm['moyenne_avec_coefficient'] !== null)
                            {{ number_format($dm['moyenne_avec_coefficient'], 2) }}
                        @else 
                            —
                        @endif
                    </td>
                @endif
                {{-- Appréciation --}}
                <td>
                    @if($dm['moyenne_generale'] !== null)
                        {{ $parametres->getMention((float) $dm['moyenne_generale']) }}
                    @else 
                        —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="left" colspan="{{ $estPremierCycle ? 6 : 8 }}">MOYENNE GÉNÉRALE DU SEMESTRE {{ $semestre }}</td>
                <td colspan="{{ $estPremierCycle ? 2 : 2 }}" style="font-size:11px;">
                    {{ $moyenne !== null ? number_format($moyenne, 2) . ' / 20' : '—' }}
                </td>
                <td colspan="{{ $estPremierCycle ? 1 : 1 }}">
                    Rang : {{ $rang ?? '—' }}{{ $rang == 1 ? 'er' : 'ème' }} / {{ $effectif }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- RÉSUMÉ --}}
    <table class="resume-table">
        <tr>
            <td>
                <div class="resume-cell" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <span class="resume-label" style="color:#1d4ed8;">Moyenne S{{ $semestre }}</span>
                    <span class="resume-value" style="color:{{ ($moyenne ?? 0) >= 10 ? '#15803d' : '#dc2626' }};">
                        {{ $moyenne !== null ? number_format($moyenne, 2) : '—' }}<span style="font-size:9px;">/20</span>
                    </span>
                </div>
            </td>
            <td>
                <div class="resume-cell" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <span class="resume-label" style="color:#15803d;">Rang</span>
                    <span class="resume-value" style="color:#15803d;">
                        {{ $rang ?? '—' }}<span style="font-size:9px;">/ {{ $effectif }}</span>
                    </span>
                </div>
            </td>
            <td>
                <div class="resume-cell" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <span class="resume-label" style="color:#475569;">Mention</span>
                    @if($mention)
                    @php
                        $mc = match($mention) {
                            'Excellent'  => 'm-exc',
                            'Très bien'  => 'm-tbi',
                            'Bien'       => 'm-bi',
                            'Assez bien' => 'm-ab',
                            'Passable'   => 'm-pa',
                            default      => 'm-ins',
                        };
                    @endphp
                    <span class="mention-badge {{ $mc }}">{{ $mention }}</span>
                    @else —
                    @endif
                </div>
            </td>
            <td>
                <div class="resume-cell" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <span class="resume-label" style="color:#475569;">Moy. de classe</span>
                    <span class="resume-value" style="color:#1e40af;">
                        {{ number_format($moyenneClasse, 2) }}<span style="font-size:9px;">/20</span>
                    </span>
                </div>
            </td>
        </tr>
    </table>

    {{-- APPRÉCIATION --}}
    <div style="border:1px solid #e2e8f0; border-radius:5px; padding:7px 10px; margin-bottom:10px; background:#fafafa;">
        <p style="font-size:7px; font-weight:700; color:#4c669a; text-transform:uppercase; margin-bottom:5px;">Appréciation du Professeur Principal</p>
        <div style="height:25px;"></div>
    </div>

    {{-- SIGNATURES --}}
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-box"><div class="sig-label">Le Professeur Principal</div></div>
            </td>
            <td style="text-align:center;">
                <div class="sig-box"><div class="sig-label">Cachet de l'établissement</div></div>
            </td>
            <td style="text-align:right;">
                <div class="sig-box"><div class="sig-label">Le Directeur</div></div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer-line">
        {{ $parametres->nom_etablissement }} · {{ $parametres->ville }}, {{ $parametres->pays }}
        @if($parametres->telephone) · Tél : {{ $parametres->telephone }} @endif
        @if($parametres->email) · {{ $parametres->email }} @endif
    </div>

</div>
</body>
</html>