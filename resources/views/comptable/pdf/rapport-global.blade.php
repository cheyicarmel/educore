<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
        .page { padding: 28px 36px; }

        /* En-tête */
        .header-table { width: 100%; border-bottom: 3px solid #2b6cee; padding-bottom: 14px; margin-bottom: 20px; }
        .school-name { font-size: 20px; font-weight: 800; color: #2b6cee; }
        .school-sub { font-size: 9px; color: #4c669a; margin-top: 2px; }
        .school-year { font-size: 11px; font-weight: 700; color: #0d121b; margin-top: 6px; }
        .doc-title { font-size: 17px; font-weight: 800; color: #0d121b; text-transform: uppercase; letter-spacing: 1px; text-align: right; }
        .doc-date { font-size: 10px; color: #4c669a; text-align: right; margin-top: 4px; }

        /* KPIs */
        .kpi-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 20px; }
        .kpi-cell { padding: 12px 14px; border-radius: 8px; text-align: center; }
        .kpi-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
        .kpi-value { font-size: 14px; font-weight: 800; display: block; }
        .kpi-currency { font-size: 8px; display: block; margin-top: 2px; }
        .cell-blue { background: #eff6ff; border: 1px solid #bfdbfe; }
        .cell-blue .kpi-label { color: #1d4ed8; }
        .cell-blue .kpi-value { color: #1e40af; }
        .cell-blue .kpi-currency { color: #3b82f6; }
        .cell-green { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .cell-green .kpi-label { color: #15803d; }
        .cell-green .kpi-value { color: #16a34a; }
        .cell-green .kpi-currency { color: #22c55e; }
        .cell-orange { background: #fff7ed; border: 1px solid #fed7aa; }
        .cell-orange .kpi-label { color: #c2410c; }
        .cell-orange .kpi-value { color: #ea580c; }
        .cell-orange .kpi-currency { color: #f97316; }
        .cell-violet { background: #f5f3ff; border: 1px solid #ddd6fe; }
        .cell-violet .kpi-label { color: #6d28d9; }
        .cell-violet .kpi-value { color: #7c3aed; }

        /* Statuts */
        .statuts-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 20px; }
        .statut-cell { padding: 10px 14px; border-radius: 8px; text-align: center; }
        .statut-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 3px; }
        .statut-value { font-size: 20px; font-weight: 800; display: block; }
        .statut-sub { font-size: 8px; display: block; margin-top: 2px; }

        /* Section titre */
        .section-title { font-size: 9px; font-weight: 800; color: #4c669a; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }

        /* Tableau classes */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr { background: #f8fafc; }
        .data-table thead th { padding: 8px 10px; font-size: 8px; font-weight: 800; color: #4c669a; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; }
        .data-table thead th.right { text-align: right; }
        .data-table thead th.center { text-align: center; }
        .data-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody td { padding: 8px 10px; font-size: 10px; color: #0d121b; vertical-align: middle; }
        .data-table tbody td.right { text-align: right; font-weight: 700; }
        .data-table tbody td.center { text-align: center; }
        .data-table tfoot tr { background: #f0f7ff; border-top: 2px solid #bfdbfe; }
        .data-table tfoot td { padding: 8px 10px; font-size: 10px; font-weight: 800; color: #1e40af; }
        .data-table tfoot td.right { text-align: right; }

        /* Badge taux */
        .badge-ok { background: #dcfce7; color: #15803d; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }
        .badge-mid { background: #fef9c3; color: #a16207; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }
        .badge-ko { background: #fee2e2; color: #b91c1c; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; }

        /* Footer */
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer-main { font-size: 8px; color: #94a3b8; }
        .footer-mention { font-size: 7px; color: #cbd5e1; margin-top: 3px; }
    </style>
</head>
<body>
<div class="page">

    {{-- EN-TÊTE --}}
    <table class="header-table">
        <tr>
            <td style="width:55%; vertical-align:top;">
                <div class="school-name">EduCore</div>
                <div class="school-sub">Système de Gestion Scolaire</div>
                <div class="school-year">{{ $anneeActive?->libelle }}</div>
            </td>
            <td style="width:45%; vertical-align:top;">
                <div class="doc-title">Rapport Financier Global</div>
                <div class="doc-date">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            </td>
        </tr>
    </table>

    {{-- KPIs --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-cell cell-blue">
                <span class="kpi-label">Total Dû</span>
                <span class="kpi-value">{{ number_format($totalDu, 0, ',', ' ') }}</span>
                <span class="kpi-currency">FCFA</span>
            </td>
            <td class="kpi-cell cell-green">
                <span class="kpi-label">Total Encaissé</span>
                <span class="kpi-value">{{ number_format($totalPaye, 0, ',', ' ') }}</span>
                <span class="kpi-currency">FCFA</span>
            </td>
            <td class="kpi-cell cell-orange">
                <span class="kpi-label">Soldes Restants</span>
                <span class="kpi-value">{{ number_format($totalSolde, 0, ',', ' ') }}</span>
                <span class="kpi-currency">FCFA</span>
            </td>
            <td class="kpi-cell cell-violet">
                <span class="kpi-label">Taux de Recouvrement</span>
                <span class="kpi-value">{{ $tauxRecouvrement }}%</span>
            </td>
        </tr>
    </table>

    {{-- STATUTS ÉLÈVES --}}
    <div class="section-title">Situation des Élèves</div>
    <table class="statuts-table" style="margin-bottom:20px;">
        <tr>
            <td class="statut-cell" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                <span class="statut-label" style="color:#15803d;">Élèves Soldés</span>
                <span class="statut-value" style="color:#16a34a;">{{ $elevesAJour }}</span>
                <span class="statut-sub" style="color:#22c55e;">sur {{ $totalEleves }} élèves</span>
            </td>
            <td class="statut-cell" style="background:#fff7ed; border:1px solid #fed7aa;">
                <span class="statut-label" style="color:#c2410c;">Élèves en Retard</span>
                <span class="statut-value" style="color:#ea580c;">{{ $elevesRetard }}</span>
                <span class="statut-sub" style="color:#f97316;">sur {{ $totalEleves }} élèves</span>
            </td>
            <td class="statut-cell" style="background:#f8fafc; border:1px solid #e2e8f0;">
                <span class="statut-label" style="color:#4c669a;">Total Paiements</span>
                <span class="statut-value" style="color:#0d121b;">{{ $nombrePaiements }}</span>
                <span class="statut-sub" style="color:#94a3b8;">enregistrés</span>
            </td>
        </tr>
    </table>

    {{-- RÉPARTITION PAR CLASSE --}}
    <div class="section-title">Répartition par Classe</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Classe</th>
                <th class="center">Élèves</th>
                <th class="right">Total Dû</th>
                <th class="right">Total Encaissé</th>
                <th class="right">Solde Restant</th>
                <th class="center">Taux</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parClasse as $c)
            <tr>
                <td style="font-weight:700;">{{ $c->nom }}</td>
                <td class="center">{{ $c->nb_eleves }}</td>
                <td class="right">{{ number_format($c->total_du, 0, ',', ' ') }} FCFA</td>
                <td class="right">{{ number_format($c->total_paye, 0, ',', ' ') }} FCFA</td>
                <td class="right">{{ number_format($c->solde, 0, ',', ' ') }} FCFA</td>
                <td class="center">
                    <span class="{{ $c->taux >= 80 ? 'badge-ok' : ($c->taux >= 50 ? 'badge-mid' : 'badge-ko') }}">
                        {{ $c->taux }}%
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="font-weight:800;">TOTAL</td>
                <td class="right">{{ $totalEleves }}</td>
                <td class="right">{{ number_format($totalDu, 0, ',', ' ') }} FCFA</td>
                <td class="right">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</td>
                <td class="right">{{ number_format($totalSolde, 0, ',', ' ') }} FCFA</td>
                <td class="center">
                    <span class="{{ $tauxRecouvrement >= 80 ? 'badge-ok' : ($tauxRecouvrement >= 50 ? 'badge-mid' : 'badge-ko') }}">
                        {{ $tauxRecouvrement }}%
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-main">Document généré automatiquement par EduCore — Ne constitue pas un document contractuel.</div>
        <div class="footer-mention">EduCore · Système de Gestion Scolaire · {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

</div>
</body>
</html>