<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1a1a2e; }
        .page { padding: 20px 24px; }

        .header-table { width: 100%; border-bottom: 3px solid #2b6cee; padding-bottom: 10px; margin-bottom: 12px; }
        .school-name { font-size: 15px; font-weight: 800; color: #2b6cee; }
        .school-sub { font-size: 8px; color: #4c669a; margin-top: 2px; }
        .doc-title { font-size: 13px; font-weight: 800; color: #0d121b; text-transform: uppercase; text-align: right; }
        .doc-sub { font-size: 8px; color: #4c669a; text-align: right; margin-top: 3px; }

        .eleve-band { width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 12px; border-left: 4px solid #7c3aed; }
        .eleve-band td { padding: 8px 12px; }
        .info-label { color: #4c669a; font-weight: 700; text-transform: uppercase; font-size: 7px; display: block; }
        .info-value { color: #0d121b; font-weight: 800; font-size: 11px; }

        .resume-table { width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 16px; }
        .resume-cell { padding: 14px; border-radius: 8px; text-align: center; }
        .resume-label { font-size: 7px; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px; }
        .resume-value { font-size: 20px; font-weight: 800; display: block; }

        .semestres-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .semestres-table thead tr { background: #1e40af; }
        .semestres-table thead th { padding: 8px; font-size: 8px; font-weight: 700; color: white; text-align: center; border: 1px solid #1d4ed8; text-transform: uppercase; }
        .semestres-table thead th.left { text-align: left; }
        .semestres-table tbody td { padding: 10px 8px; font-size: 11px; border: 1px solid #e2e8f0; text-align: center; vertical-align: middle; }
        .semestres-table tbody td.left { text-align: left; }
        .semestres-table tbody tr:nth-child(even) { background: #f8fafc; }

        .ok { color: #15803d; font-weight: 800; }
        .ko { color: #dc2626; font-weight: 800; }

        .decision-pass { background: #dcfce7; color: #15803d; font-weight: 800; padding: 4px 12px; border-radius: 12px; font-size: 11px; }
        .decision-doub { background: #fee2e2; color: #b91c1c; font-weight: 800; padding: 4px 12px; border-radius: 12px; font-size: 11px; }

        .mention-badge { font-size: 10px; font-weight: 800; padding: 3px 10px; border-radius: 10px; }
        .m-exc  { background: #fef9c3; color: #a16207; }
        .m-tbi  { background: #f5f3ff; color: #6d28d9; }
        .m-bi   { background: #dcfce7; color: #15803d; }
        .m-ab   { background: #dbeafe; color: #1d4ed8; }
        .m-pa   { background: #fef3c7; color: #d97706; }
        .m-ins  { background: #fee2e2; color: #b91c1c; }

        .sig-table { width: 100%; margin-top: 20px; }
        .sig-table td { width: 33%; padding: 0 6px; vertical-align: top; }
        .sig-box { border: 1px dashed #cbd5e1; border-radius: 4px; padding: 8px 10px; height: 60px; }
        .sig-label { font-size: 7px; font-weight: 700; color: #4c669a; text-transform: uppercase; }
        .footer-line { margin-top: 10px; padding-top: 6px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 7px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="page">

    {{-- EN-TÊTE --}}
    <table class="header-table">
        <tr>
            <td style="width:60%; vertical-align:top;">
                @if($parametres->logo)
                <img src="{{ public_path('storage/' . $parametres->logo) }}" style="height:38px; margin-bottom:4px;"/>
                @endif
                <div class="school-name">{{ $parametres->nom_etablissement }}</div>
                @if($parametres->slogan)
                <div class="school-sub"><em>{{ $parametres->slogan }}</em></div>
                @endif
                <div class="school-sub">{{ $parametres->adresse ?? '' }} — {{ $parametres->ville }}, {{ $parametres->pays }}</div>
                @if($parametres->telephone)
                <div class="school-sub">Tél : {{ $parametres->telephone }}{{ $parametres->telephone2 ? ' / ' . $parametres->telephone2 : '' }}</div>
                @endif
            </td>
            <td style="width:40%; vertical-align:top;">
                <div class="doc-title">Bulletin Annuel</div>
                <div class="doc-sub">Année {{ $anneeActive?->libelle }}</div>
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
                <span class="info-label">Effectif classe</span>
                <span class="info-value">{{ $effectif }} élèves</span>
            </td>
        </tr>
    </table>

    {{-- RÉSUMÉ ANNUEL --}}
    <table class="resume-table">
        <tr>
            <td>
                <div class="resume-cell" style="background:#eff6ff; border:1px solid #bfdbfe;">
                    <span class="resume-label" style="color:#1d4ed8;">Moyenne Annuelle</span>
                    <span class="resume-value" style="color:{{ ($moy_annuelle ?? 0) >= 10 ? '#15803d' : '#dc2626' }};">
                        {{ $moy_annuelle !== null ? number_format($moy_annuelle, 2) : '—' }}<span style="font-size:11px;">/20</span>
                    </span>
                </div>
            </td>
            <td>
                <div class="resume-cell" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <span class="resume-label" style="color:#15803d;">Rang Final</span>
                    <span class="resume-value" style="color:#15803d;">
                        {{ $rang ?? '—' }}<span style="font-size:11px;">/ {{ $effectif }}</span>
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
                <div class="resume-cell" style="background:#faf5ff; border:1px solid #e9d5ff;">
                    <span class="resume-label" style="color:#7c3aed;">Décision</span>
                    @if($decision)
                    <span class="{{ $decision === 'passant' ? 'decision-pass' : 'decision-doub' }}">
                        {{ ucfirst($decision) }}
                    </span>
                    @else —
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- TABLEAU SEMESTRES --}}
    <table class="semestres-table">
        <thead>
            <tr>
                <th class="left">Période</th>
                <th>Moyenne</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="left"><strong>Semestre 1</strong></td>
                <td>
                    @if($moy_s1 !== null)
                    <span class="{{ $moy_s1 >= 10 ? 'ok' : 'ko' }}">{{ number_format($moy_s1, 2) }} / 20</span>
                    @else —
                    @endif
                </td>
                <td>
                    @if($moy_s1 !== null)
                        {{ $parametres->getMention((float) $moy_s1) }}
                    @else 
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td class="left"><strong>Semestre 2</strong></td>
                <td>
                    @if($moy_s2 !== null)
                        <span class="{{ $moy_s2 >= 10 ? 'ok' : 'ko' }}">{{ number_format($moy_s2, 2) }} / 20</span>
                    @else 
                        —
                    @endif
                </td>
                <td>
                    @if($moy_s2 !== null)
                        {{ $parametres->getMention((float) $moy_s2) }}
                    @else 
                        —
                    @endif
                </td>
            </tr>
            <tr style="background:#1e3a8a;">
                <td class="left" style="color:white; font-weight:800; font-size:10px;">MOYENNE ANNUELLE</td>
                <td style="color:white; font-weight:800; font-size:13px;">
                    {{ $moy_annuelle !== null ? number_format($moy_annuelle, 2) . ' / 20' : '—' }}
                </td>
                <td style="color:white; font-weight:800;">
                    Rang {{ $rang ?? '—' }}{{ $rang == 1 ? 'er' : 'ème' }} / {{ $effectif }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- APPRÉCIATION --}}
    <div style="border:1px solid #e2e8f0; border-radius:6px; padding:8px 12px; margin-bottom:14px; background:#fafafa;">
        <p style="font-size:7px; font-weight:700; color:#4c669a; text-transform:uppercase; margin-bottom:6px;">Appréciation du Conseil de Classe</p>
        <div style="height:30px;"></div>
    </div>

    {{-- SIGNATURES --}}
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-box">
                    <div class="sig-label">Le Professeur Principal</div>
                </div>
            </td>
            <td style="text-align:center;">
                <div class="sig-box">
                    <div class="sig-label">Cachet de l'établissement</div>
                </div>
            </td>
            <td style="text-align:right;">
                <div class="sig-box">
                    <div class="sig-label">Le Directeur</div>
                </div>
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