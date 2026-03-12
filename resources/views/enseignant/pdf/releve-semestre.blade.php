<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1a1a2e; }
        .page { padding: 20px 24px; }

        /* En-tête */
        .header-table { width: 100%; border-bottom: 3px solid #2b6cee; padding-bottom: 10px; margin-bottom: 14px; }
        .school-name { font-size: 16px; font-weight: 800; color: #2b6cee; }
        .school-sub { font-size: 8px; color: #4c669a; margin-top: 2px; }
        .doc-title { font-size: 14px; font-weight: 800; color: #0d121b; text-transform: uppercase; text-align: right; }
        .doc-sub { font-size: 9px; color: #4c669a; text-align: right; margin-top: 3px; }

        /* Infos classe */
        .info-band { width: 100%; background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 6px; margin-bottom: 14px; }
        .info-band td { padding: 7px 12px; font-size: 9px; }
        .info-label { color: #4c669a; font-weight: 700; text-transform: uppercase; font-size: 8px; display: block; }
        .info-value { color: #0d121b; font-weight: 800; font-size: 10px; }

        /* Tableau */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr { background: #1e40af; }
        .data-table thead th { padding: 6px 5px; font-size: 8px; font-weight: 700; color: white; text-transform: uppercase; letter-spacing: 0.3px; border: 1px solid #1d4ed8; }
        .data-table thead th.left { text-align: left; }
        .data-table thead th.center { text-align: center; }
        .data-table thead th.moy { background: #1d4ed8; }
        .data-table tbody tr:nth-child(even) { background: #f8fafc; }
        .data-table tbody tr:nth-child(odd) { background: #ffffff; }
        .data-table tbody td { padding: 5px; font-size: 8px; color: #0d121b; border: 1px solid #e2e8f0; vertical-align: middle; }
        .data-table tbody td.center { text-align: center; }
        .data-table tbody td.moy-cell { background: #eff6ff; text-align: center; border-left: 2px solid #93c5fd; border-right: 2px solid #93c5fd; }

        /* Badges */
        .badge-ok  { color: #15803d; font-weight: 800; }
        .badge-ko  { color: #dc2626; font-weight: 800; }
        .mention   { font-size: 7px; font-weight: 700; padding: 1px 5px; border-radius: 8px; }
        .m-exc  { background: #fef9c3; color: #a16207; }
        .m-tbi  { background: #f5f3ff; color: #6d28d9; }
        .m-bi   { background: #dcfce7; color: #15803d; }
        .m-ab   { background: #dbeafe; color: #1d4ed8; }
        .m-pa   { background: #fef3c7; color: #d97706; }
        .m-ins  { background: #fee2e2; color: #b91c1c; }

        /* Footer */
        .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e2e8f0; }
        .footer table { width: 100%; }
        .footer td { font-size: 7px; color: #94a3b8; vertical-align: top; }
        .sig-box { border: 1px dashed #cbd5e1; border-radius: 4px; padding: 6px 10px; height: 50px; }
        .sig-label { font-size: 7px; font-weight: 700; color: #4c669a; text-transform: uppercase; margin-bottom: 3px; }
    </style>
</head>
<body>
<div class="page">

    {{-- EN-TÊTE --}}
    <table class="header-table">
        <tr>
            <td style="width:60%; vertical-align:top;">
                @if($parametres->logo)
                <img src="{{ public_path('storage/' . $parametres->logo) }}" style="height:40px; margin-bottom:4px;"/>
                @endif
                <div class="school-name">{{ $parametres->nom_etablissement }}</div>
                @if($parametres->slogan)
                <div class="school-sub">{{ $parametres->slogan }}</div>
                @endif
                <div class="school-sub">{{ $parametres->ville }} — {{ $parametres->pays }}</div>
            </td>
            <td style="width:40%; vertical-align:top;">
                <div class="doc-title">Relevé de Notes</div>
                <div class="doc-sub">Semestre {{ $semestre }} — Année {{ $anneeActive?->libelle }}</div>
                <div class="doc-sub">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            </td>
        </tr>
    </table>

    {{-- INFOS CLASSE --}}
    <table class="info-band">
        <tr>
            <td>
                <span class="info-label">Classe</span>
                <span class="info-value">{{ $classe->nom }}</span>
            </td>
            <td>
                <span class="info-label">Série</span>
                <span class="info-value">{{ $classe->serie->libelle ?? '—' }}</span>
            </td>
            <td>
                <span class="info-label">Effectif</span>
                <span class="info-value">{{ $eleves->count() }} élèves</span>
            </td>
            <td>
                <span class="info-label">Semestre</span>
                <span class="info-value">Semestre {{ $semestre }}</span>
            </td>
            <td>
                <span class="info-label">Moyenne de classe</span>
                <span class="info-value">{{ number_format($eleves->avg('moyenne'), 2) }}/20</span>
            </td>
        </tr>
    </table>

    {{-- TABLEAU --}}
    <table class="data-table">
        <thead>
            <tr>
                <th class="left" style="width:30px;">Rang</th>
                <th class="left" style="min-width:120px;">Élève</th>
                @foreach($attributions as $attr)
                <th class="center" style="max-width:60px;">{{ \Illuminate\Support\Str::limit($attr->matiere->nom, 7) }}</th>
                @endforeach
                <th class="center moy" style="width:50px;">Moy. Gén.</th>
                <th class="center" style="width:55px;">Mention</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eleves as $item)
            @php
                $eleve = $item['eleve'];
                $mentionClass = match($item['mention'] ?? '') {
                    'Excellent'   => 'm-exc',
                    'Très bien'   => 'm-tbi',
                    'Bien'        => 'm-bi',
                    'Assez bien'  => 'm-ab',
                    'Passable'    => 'm-pa',
                    default       => 'm-ins',
                };
            @endphp
            <tr>
                <td class="center">
                    <strong>{{ $item['rang'] ?? '—' }}</strong>
                </td>
                <td>
                    <strong>{{ $eleve->nom }}</strong> {{ $eleve->prenom }}
                </td>
                @foreach($item['detail_matieres'] as $dm)
                <td class="center">
                    @if($dm['moyenne_generale'] !== null)
                    <span class="{{ $dm['moyenne_generale'] >= 10 ? 'badge-ok' : 'badge-ko' }}">
                        {{ number_format($dm['moyenne_generale'], 2) }}
                    </span>
                    @if($dm['moyenne_avec_coefficient'] !== null)
                    <br><span style="font-size:7px; color:#94a3b8;">({{ number_format($dm['moyenne_avec_coefficient'], 2) }})</span>
                    @endif
                    @else
                    —
                    @endif
                </td>
                @endforeach
                <td class="moy-cell">
                    @if($item['moyenne'] !== null)
                    <strong class="{{ $item['moyenne'] >= 10 ? 'badge-ok' : 'badge-ko' }}" style="font-size:10px;">
                        {{ number_format($item['moyenne'], 2) }}
                    </strong>
                    @else
                    —
                    @endif
                </td>
                <td class="center">
                    @if($item['mention'])
                    <span class="mention {{ $mentionClass }}">{{ $item['mention'] }}</span>
                    @else
                    —
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <table>
            <tr>
                <td style="width:33%;">
                    <div class="sig-box">
                        <div class="sig-label">Le Professeur Principal</div>
                    </div>
                </td>
                <td style="width:33%; text-align:center; padding: 0 8px;">
                    <div class="sig-box">
                        <div class="sig-label">Cachet de l'établissement</div>
                    </div>
                </td>
                <td style="width:33%; text-align:right;">
                    <div class="sig-box">
                        <div class="sig-label">Le Directeur</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top:6px; text-align:center; color:#cbd5e1; font-size:7px;">
                    {{ $parametres->nom_etablissement }} · {{ $parametres->ville }}, {{ $parametres->pays }}
                    @if($parametres->telephone) · {{ $parametres->telephone }} @endif
                    @if($parametres->email) · {{ $parametres->email }} @endif
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>