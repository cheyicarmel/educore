<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
        .page { padding: 28px 36px; }

        /* En-tête */
        .header-table { width: 100%; border-bottom: 3px solid #2b6cee; padding-bottom: 14px; margin-bottom: 16px; }
        .school-name { font-size: 20px; font-weight: 800; color: #2b6cee; }
        .school-sub { font-size: 9px; color: #4c669a; margin-top: 2px; }
        .school-year { font-size: 11px; font-weight: 700; color: #0d121b; margin-top: 6px; }
        .recu-title { font-size: 17px; font-weight: 800; color: #0d121b; text-transform: uppercase; letter-spacing: 1px; text-align: right; }
        .recu-ref { font-size: 11px; font-weight: 700; color: #2b6cee; text-align: right; margin-top: 4px; }
        .recu-date { font-size: 10px; color: #4c669a; text-align: right; margin-top: 2px; }

        /* Barre statut */
        .status-bar { width: 100%; background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 6px; margin-bottom: 16px; }
        .status-bar td { padding: 8px 14px; font-size: 10px; }
        .status-label { color: #4c669a; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-value-ok { color: #16a34a; font-size: 12px; font-weight: 800; text-align: right; }
        .status-value-ko { color: #dc2626; font-size: 12px; font-weight: 800; text-align: right; }

        /* Sections infos */
        .section-title { font-size: 8px; font-weight: 800; color: #4c669a; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 6px; }
        .info-table { width: 100%; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-key { font-size: 9px; color: #4c669a; font-weight: 600; width: 45%; }
        .info-val { font-size: 9px; font-weight: 700; color: #0d121b; text-align: right; }
        .info-separator { border-bottom: 1px dashed #f1f5f9; }

        /* Bloc montant */
        .amount-block { background: #2b6cee; border-radius: 8px; padding: 14px 20px; margin: 14px 0; text-align: center; }
        .amount-label { font-size: 8px; font-weight: 700; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 1px; }
        .amount-value { font-size: 26px; font-weight: 800; color: #fff; margin-top: 4px; }
        .amount-currency { font-size: 12px; color: rgba(255,255,255,0.8); }

        /* Récap solde */
        .solde-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 14px; }
        .solde-cell { padding: 8px 10px; border-radius: 6px; text-align: center; }
        .solde-label { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 3px; }
        .solde-value { font-size: 12px; font-weight: 800; display: block; }
        .solde-currency { font-size: 7px; color: #94a3b8; display: block; margin-top: 1px; }
        .cell-du { background: #f8fafc; border: 1px solid #e2e8f0; }
        .cell-du .solde-label { color: #4c669a; }
        .cell-du .solde-value { color: #0d121b; }
        .cell-paye { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .cell-paye .solde-label { color: #15803d; }
        .cell-paye .solde-value { color: #16a34a; }
        .cell-restant-ok { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .cell-restant-ok .solde-label { color: #15803d; }
        .cell-restant-ok .solde-value { color: #16a34a; }
        .cell-restant-ko { background: #fff7ed; border: 1px solid #fed7aa; }
        .cell-restant-ko .solde-label { color: #c2410c; }
        .cell-restant-ko .solde-value { color: #ea580c; }

        /* Signatures */
        .sig-table { width: 100%; border-collapse: separate; border-spacing: 8px; margin-top: 18px; }
        .sig-cell { border: 1px dashed #cbd5e1; border-radius: 6px; padding: 10px 12px; vertical-align: top; width: 33.33%; height: 90px; }
        .sig-cell-label { font-size: 8px; font-weight: 700; color: #4c669a; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #f1f5f9; padding-bottom: 5px; margin-bottom: 6px; }
        .sig-name { font-size: 10px; font-weight: 700; color: #0d121b; }
        .sig-role { font-size: 8px; color: #4c669a; margin-top: 2px; }

        /* Footer */
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer-main { font-size: 8px; color: #94a3b8; }
        .footer-mention { font-size: 7px; color: #cbd5e1; margin-top: 3px; }

        /* Watermark */
        .watermark { position: fixed; bottom: 100px; right: 30px; opacity: 0.05; font-size: 55px; font-weight: 900; color: #10b981; transform: rotate(-30deg); text-transform: uppercase; letter-spacing: 4px; }
    </style>
</head>
<body>
<div class="page">

    @if($suivi->solde_restant <= 0)
        <div class="watermark">Soldé</div>
    @endif

    {{-- EN-TÊTE --}}
    <table class="header-table">
        <tr>
            <td style="width:55%; vertical-align:top;">
                <div class="school-name">EduCore</div>
                <div class="school-sub">Système de Gestion Scolaire</div>
                <div class="school-year">{{ $anneeActive?->libelle }}</div>
            </td>
            <td style="width:45%; vertical-align:top;">
                <div class="recu-title">Reçu de Paiement</div>
                <div class="recu-ref">Réf : {{ $paiement->reference }}</div>
                <div class="recu-date">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- STATUT --}}
    <table class="status-bar">
        <tr>
            <td class="status-label">Statut du compte</td>
            <td class="{{ $suivi->statut === 'solde' ? 'status-value-ok' : 'status-value-ko' }}">
                @if($suivi->statut === 'solde')
                    ✓ Compte soldé — À jour
                @else
                    ✗ Solde restant à régler
                @endif
            </td>
        </tr>
    </table>

    {{-- INFOS ÉLÈVE + PAIEMENT --}}
    <table style="width:100%; margin-bottom:6px;">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:12px;">
                <div class="section-title">Informations de l'élève</div>
                <table class="info-table">
                    <tr class="info-separator">
                        <td class="info-key">Nom complet</td>
                        <td class="info-val">{{ $eleve->user->prenom }} {{ $eleve->user->nom }}</td>
                    </tr>
                    <tr class="info-separator">
                        <td class="info-key">Classe</td>
                        <td class="info-val">{{ $inscription->classe->nom }}</td>
                    </tr>
                    @if($inscription->classe->serie)
                    <tr class="info-separator">
                        <td class="info-key">Série</td>
                        <td class="info-val">{{ $inscription->classe->serie->libelle }}</td>
                    </tr>
                    @endif
                    @if($eleve->matricule)
                    <tr class="info-separator">
                        <td class="info-key">Matricule</td>
                        <td class="info-val">{{ $eleve->matricule }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="info-key">Année académique</td>
                        <td class="info-val">{{ $anneeActive?->libelle }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; padding-left:12px; border-left:1px dashed #e2e8f0;">
                <div class="section-title">Détails du paiement</div>
                <table class="info-table">
                    <tr class="info-separator">
                        <td class="info-key">Référence</td>
                        <td class="info-val">{{ $paiement->reference }}</td>
                    </tr>
                    <tr class="info-separator">
                        <td class="info-key">Date</td>
                        <td class="info-val">{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                    </tr>
                    <tr class="info-separator">
                        <td class="info-key">Mode de paiement</td>
                        <td class="info-val">{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
                    </tr>
                    <tr>
                        <td class="info-key">Enregistré par</td>
                        <td class="info-val">{{ $comptable->prenom }} {{ $comptable->nom }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- MONTANT --}}
    <div class="amount-block">
        <div class="amount-label">Montant encaissé</div>
        <div class="amount-value">
            {{ number_format($paiement->montant, 0, ',', ' ') }}
            <span class="amount-currency">FCFA</span>
        </div>
    </div>

    {{-- RÉCAP SOLDE --}}
    <table class="solde-table">
        <tr>
            <td class="solde-cell cell-du">
                <span class="solde-label">Total Dû</span>
                <span class="solde-value">{{ number_format($suivi->total_du, 0, ',', ' ') }}</span>
                <span class="solde-currency">FCFA</span>
            </td>
            <td class="solde-cell cell-paye">
                <span class="solde-label">Total Payé</span>
                <span class="solde-value">{{ number_format($suivi->total_paye, 0, ',', ' ') }}</span>
                <span class="solde-currency">FCFA</span>
            </td>
            <td class="solde-cell {{ $suivi->solde_restant <= 0 ? 'cell-restant-ok' : 'cell-restant-ko' }}">
                <span class="solde-label">Solde Restant</span>
                <span class="solde-value">{{ number_format($suivi->solde_restant, 0, ',', ' ') }}</span>
                <span class="solde-currency">FCFA</span>
            </td>
        </tr>
    </table>

    {{-- SIGNATURES --}}
    <table class="sig-table">
        <tr>
            <td class="sig-cell">
                <div class="sig-cell-label">Signature du Comptable</div>
                <div class="sig-name">{{ $comptable->prenom }} {{ $comptable->nom }}</div>
                <div class="sig-role">Comptable — EduCore</div>
            </td>
            <td class="sig-cell">
                <div class="sig-cell-label">Cachet de l'établissement</div>
            </td>
            <td class="sig-cell">
                <div class="sig-cell-label">Signature du Payeur</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-main">Ce reçu constitue une preuve officielle de paiement. Conservez-le précieusement.</div>
        <div class="footer-mention">EduCore — Système de Gestion Scolaire · Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

</div>
</body>
</html>