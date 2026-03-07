<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Attribution;
use App\Models\Inscription;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneSemestre;
use App\Models\MoyenneAnnuelle;
use App\Models\Note;
use App\Models\SuiviFinancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user        = Auth::user();
        $anneeActive = AnneeAcademique::active()->first();
        $eleve       = $user->eleve;
        $vue         = $request->input('vue', '1'); // '1', '2', 'annuel'

        // Inscription cette année
        $inscription = Inscription::where('eleve_id', $eleve->id)
            ->where('annee_academique_id', $anneeActive?->id)
            ->with('classe.serie')
            ->first();

        $classe   = $inscription?->classe;
        $effectif = $classe
            ? Inscription::where('classe_id', $classe->id)
                ->where('annee_academique_id', $anneeActive?->id)
                ->count()
            : 0;

        // ── Données financières ────────────────────────────────
        $suivi = $inscription
            ? SuiviFinancier::where('inscription_id', $inscription->id)->first()
            : null;

        $totalDu         = $suivi ? (float) $suivi->total_du : 0;
        $totalPaye       = $suivi ? (float) $suivi->total_paye : 0;
        $soldeRestant    = $suivi ? (float) $suivi->solde_restant : 0;
        $tauxPaiement    = $totalDu > 0 ? min(100, round(($totalPaye / $totalDu) * 100)) : 0;
        $statutFinancier = $suivi?->statut ?? 'en_retard';

        // ── Valeurs par défaut ─────────────────────────────────
        $semestreActif    = null;
        $moyenneGenerale  = null;
        $rang             = null;
        $notesRecues      = 0;
        $notesAttendues   = 0;
        $moyennesParMatiere = collect();
        // annuel
        $moyenneAnnuelle    = null;
        $rangAnnuel         = null;
        $decisionAnnuelle   = null;
        $moyennesAnnuellesParMatiere = collect();

        // ── VUE SEMESTRE ───────────────────────────────────────
        if ($vue === '1' || $vue === '2') {
            $semestreActif = (int) $vue;

            $moyenneSemestre = $inscription
                ? MoyenneSemestre::where('inscription_id', $inscription->id)
                    ->where('numero_semestre', $semestreActif)
                    ->first()
                : null;

            $moyenneGenerale = $moyenneSemestre ? (float) $moyenneSemestre->valeur : null;
            $rang            = $moyenneSemestre?->rang;

            // Notes reçues vs attendues
            if ($inscription) {
                $nbMatieres     = Attribution::where('classe_id', $classe->id)
                    ->where('annee_academique_id', $anneeActive?->id)
                    ->count();
                $notesAttendues = $nbMatieres * 5;
                $notesRecues    = Note::where('inscription_id', $inscription->id)
                    ->where('numero_semestre', $semestreActif)
                    ->count();
            }

            // Moyennes par matière
            if ($inscription) {
                $moyennesParMatiere = MoyenneMatiere::where('inscription_id', $inscription->id)
                    ->where('numero_semestre', $semestreActif)
                    ->with('matiere')
                    ->get()
                    ->map(fn($m) => [
                        'matiere' => $m->matiere->nom,
                        'moyenne' => (float) $m->moyenne_generale,
                    ]);
            }
        }

        // ── VUE ANNUELLE ───────────────────────────────────────
        elseif ($vue === 'annuel') {
            $moyAnnuelle = $inscription
                ? MoyenneAnnuelle::where('inscription_id', $inscription->id)->first()
                : null;

            $moyenneAnnuelle  = $moyAnnuelle ? (float) $moyAnnuelle->valeur : null;
            $rangAnnuel       = $moyAnnuelle?->rang;
            $decisionAnnuelle = $moyAnnuelle?->decision;

            // Moyennes par matière (on prend S1 + S2 et on fait la moyenne des deux)
            if ($inscription) {
                $moysS1 = MoyenneMatiere::where('inscription_id', $inscription->id)
                    ->where('numero_semestre', 1)->with('matiere')->get()->keyBy('matiere_id');
                $moysS2 = MoyenneMatiere::where('inscription_id', $inscription->id)
                    ->where('numero_semestre', 2)->with('matiere')->get()->keyBy('matiere_id');

                $toutesLesMatieresIds = $moysS1->keys()->merge($moysS2->keys())->unique();

                $moyennesAnnuellesParMatiere = $toutesLesMatieresIds->map(function ($matiereId) use ($moysS1, $moysS2) {
                    $s1  = $moysS1[$matiereId] ?? null;
                    $s2  = $moysS2[$matiereId] ?? null;
                    $nom = $s1?->matiere->nom ?? $s2?->matiere->nom;

                    $valeurs = array_filter([
                        $s1 ? (float) $s1->moyenne_generale : null,
                        $s2 ? (float) $s2->moyenne_generale : null,
                    ], fn($v) => $v !== null);

                    return [
                        'matiere' => $nom,
                        'moyenne' => count($valeurs) > 0 ? array_sum($valeurs) / count($valeurs) : null,
                        'moy_s1'  => $s1 ? (float) $s1->moyenne_generale : null,
                        'moy_s2'  => $s2 ? (float) $s2->moyenne_generale : null,
                    ];
                })->values();
            }
        }

        // ── Bulletins disponibles ──────────────────────────────
        $bulletins = collect();
        if ($inscription) {
            $moyS = MoyenneSemestre::where('inscription_id', $inscription->id)
                ->orderBy('numero_semestre')->get();

            foreach ($moyS as $ms) {
                $bulletins->push([
                    'id'       => 'semestre-' . $ms->numero_semestre,
                    'periode'  => 'Semestre ' . $ms->numero_semestre . ' — ' . $anneeActive->libelle,
                    'moyenne'  => (float) $ms->valeur,
                    'rang'     => $ms->rang,
                    'effectif' => $effectif,
                ]);
            }

            $moyAn = MoyenneAnnuelle::where('inscription_id', $inscription->id)->first();
            if ($moyAn) {
                $bulletins->push([
                    'id'       => 'annuel',
                    'periode'  => 'Annuel — ' . $anneeActive->libelle,
                    'moyenne'  => (float) $moyAn->valeur,
                    'rang'     => $moyAn->rang,
                    'effectif' => $effectif,
                ]);
            }
        }

        return view('eleve.dashboard', compact(
            'user', 'classe', 'anneeActive', 'vue', 'semestreActif',
            'moyenneGenerale', 'rang', 'effectif',
            'notesRecues', 'notesAttendues', 'moyennesParMatiere',
            'moyenneAnnuelle', 'rangAnnuel', 'decisionAnnuelle', 'moyennesAnnuellesParMatiere',
            'totalDu', 'totalPaye', 'soldeRestant', 'tauxPaiement', 'statutFinancier',
            'bulletins'
        ));
    }
}