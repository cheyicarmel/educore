<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Inscription;
use App\Models\MoyenneAnnuelle;
use App\Models\MoyenneMatiere;
use App\Models\SuiviFinancier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PassageController extends Controller
{
    const PROGRESSION = [
        '6eme'      => '5eme',
        '5eme'      => '4eme',
        '4eme'      => '3eme',
        '3eme'      => '2nde',
        '2nde'      => '1ere',
        '1ere'      => 'terminale',
        'terminale' => null,
    ];

    public function index()
    {
        $anneeActive     = AnneeAcademique::active()->first();
        $anneesSuivantes = AnneeAcademique::whereNotIn('statut', ['active', 'terminee'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Vérifier si toutes les moyennes annuelles sont calculées
        $totalInscriptions = Inscription::where('annee_academique_id', $anneeActive?->id)->count();
        $totalMoyennes     = MoyenneAnnuelle::whereHas('inscription', fn($q) =>
            $q->where('annee_academique_id', $anneeActive?->id)
        )->count();

        // Vérifier si tous les bulletins sont publiés
        $totalClasses         = Classe::where('annee_academique_id', $anneeActive?->id)->count();
        $classesBulletinsOk   = Classe::where('annee_academique_id', $anneeActive?->id)
            ->where('bulletins_publies_s1', true)
            ->where('bulletins_publies_s2', true)
            ->where('bulletins_publies_annuel', true)
            ->count();

        $moyennesPrêtes  = $totalInscriptions > 0 && $totalMoyennes >= $totalInscriptions;
        $bulletinsPublies = $totalClasses > 0 && $classesBulletinsOk >= $totalClasses;
        $pret            = $moyennesPrêtes && $bulletinsPublies;

        return view('admin.passage.index', compact(
            'anneeActive', 'anneesSuivantes',
            'totalInscriptions', 'totalMoyennes',
            'totalClasses', 'classesBulletinsOk',
            'moyennesPrêtes', 'bulletinsPublies', 'pret'
        ));
    }

    public function traiter(Request $request)
    {
        $request->validate([
            'annee_suivante_id' => 'required|exists:annees_academiques,id',
        ]);

        $anneeActive   = AnneeAcademique::active()->first();
        $anneeSuivante = AnneeAcademique::findOrFail($request->annee_suivante_id);

        // Vérifier que les bulletins sont tous publiés
        $totalClasses       = Classe::where('annee_academique_id', $anneeActive->id)->count();
        $classesBulletinsOk = Classe::where('annee_academique_id', $anneeActive->id)
            ->where('bulletins_publies_s1', true)
            ->where('bulletins_publies_s2', true)
            ->where('bulletins_publies_annuel', true)
            ->count();

        if ($classesBulletinsOk < $totalClasses) {
            return back()->with('error', 'Tous les bulletins doivent être publiés avant de lancer le passage.');
        }

        // Vérifier que les classes de la nouvelle année existent
        $classesSuivantes = Classe::where('annee_academique_id', $anneeSuivante->id)
            ->with('serie')
            ->get();

        if ($classesSuivantes->isEmpty()) {
            return back()->with('error', 'Aucune classe définie pour l\'année suivante. Créez-les d\'abord.');
        }

        DB::transaction(function () use ($anneeActive, $anneeSuivante, $classesSuivantes) {

            $inscriptions = Inscription::where('annee_academique_id', $anneeActive->id)
                ->with(['eleve', 'classe.serie', 'moyenneAnnuelle', 'suiviFinancier'])
                ->get();

            foreach ($inscriptions as $inscription) {
                $moyenneAnnuelle = $inscription->moyenneAnnuelle;
                if (!$moyenneAnnuelle) continue;

                // Éviter les doublons — si l'élève est déjà inscrit dans la nouvelle année, on skip
                $dejaInscrit = Inscription::where('eleve_id', $inscription->eleve_id)
                    ->where('annee_academique_id', $anneeSuivante->id)
                    ->exists();
                if ($dejaInscrit) continue;

                $decision      = $moyenneAnnuelle->decision;
                $classeActuelle = $inscription->classe;
                $niveauActuel  = $classeActuelle->niveau;
                $serieActuelle = $classeActuelle->serie?->code;

                if ($decision === 'passant') {
                    $niveauSuivant = self::PROGRESSION[$niveauActuel] ?? null;

                    // Terminale passant → diplômé, on ne réinscrit pas
                    if ($niveauSuivant === null) continue;

                    $serieCalculee = $this->calculerSerie($inscription, $niveauActuel, $serieActuelle);
                    $classeId      = $this->trouverClasseId($classesSuivantes, $niveauSuivant, $serieCalculee);

                } else {
                    // Doublant → même niveau et même série dans la nouvelle année
                    $classeId = $this->trouverClasseId($classesSuivantes, $niveauActuel, $serieActuelle);
                }

                if (!$classeId) continue;

                // Frais de la nouvelle classe
                $nouvelleClasse = $classesSuivantes->firstWhere('id', $classeId);
                $fraisNouveaux  = $nouvelleClasse->frais_annuels;

                // Report du solde restant de l'ancienne année
                $soldeAncien = $inscription->suiviFinancier?->solde_restant ?? 0;
                $totalDu     = $fraisNouveaux + $soldeAncien;

                // Créer la nouvelle inscription
                $nouvelleInscription = Inscription::create([
                    'eleve_id'            => $inscription->eleve_id,
                    'classe_id'           => $classeId,
                    'annee_academique_id' => $anneeSuivante->id,
                    'statut'              => 'actif',
                    'frais_annuels'       => $fraisNouveaux,
                ]);

                // Créer le suivi financier avec report du solde
                SuiviFinancier::create([
                    'inscription_id' => $nouvelleInscription->id,
                    'total_du'       => $totalDu,
                    'total_paye'     => 0,
                    'solde_restant'  => $totalDu,
                    'statut'         => 'en_retard',
                ]);
            }

            // Changer les statuts des années
            $anneeActive->update(['statut' => 'terminee']);
            $anneeSuivante->update(['statut' => 'active']);
        });

        return redirect()->route('admin.passage.index')
            ->with('success', 'Passage effectué avec succès. La nouvelle année académique est maintenant active.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function calculerSerie(Inscription $inscription, string $niveauActuel, ?string $serieActuelle): ?string
    {
        // 5ème → 4ème et 4ème → 3ème : calcul L ou C
        if (in_array($niveauActuel, ['5eme', '4eme'])) {
            return $this->calculerSerieLC($inscription);
        }

        // 3ème → 2nde
        if ($niveauActuel === '3eme') {
            if ($serieActuelle === 'L') return 'A';
            return $this->calculerSerieSecondCycle($inscription);
        }

        // 2nde → 1ère et 1ère → Tle
        if (in_array($niveauActuel, ['2nde', '1ere'])) {
            if ($serieActuelle === 'A') return 'A';
            return $this->calculerSerieSecondCycle($inscription);
        }

        // 6ème → 5ème : pas de série
        return null;
    }

    private function calculerSerieLC(Inscription $inscription): string
    {
        $moyennes = MoyenneMatiere::where('inscription_id', $inscription->id)
            ->with('matiere')
            ->get();

        $moyLitt = $moyennes->filter(fn($m) => $m->matiere?->est_litteraire)->avg('moyenne_generale') ?? 0;
        $moySci  = $moyennes->filter(fn($m) => $m->matiere?->est_scientifique)->avg('moyenne_generale') ?? 0;

        return $moyLitt >= $moySci ? 'L' : 'C';
    }

    private function calculerSerieSecondCycle(Inscription $inscription): string
    {
        $moyennes = MoyenneMatiere::where('inscription_id', $inscription->id)
            ->with('matiere')
            ->get();

        $moyMP   = $moyennes->filter(fn($m) => $m->matiere?->sous_groupe === 'maths_physique')->avg('moyenne_generale') ?? 0;
        $moySVT  = $moyennes->filter(fn($m) => $m->matiere?->sous_groupe === 'svt')->avg('moyenne_generale') ?? 0;
        $moyLitt = $moyennes->filter(fn($m) => $m->matiere?->est_litteraire)->avg('moyenne_generale') ?? 0;

        if ($moyMP > $moySVT && $moyMP > $moyLitt) return 'C';
        if ($moySVT > $moyMP) return 'D';
        return 'D';
    }

    private function trouverClasseId($classes, string $niveau, ?string $serie): ?int
    {
        // Cherche d'abord une classe avec le bon niveau ET la bonne série
        $classe = $classes->first(function ($c) use ($niveau, $serie) {
            if ($c->niveau !== $niveau) return false;
            if ($serie) return $c->serie?->code === $serie;
            return !$c->serie_id;
        });

        // Fallback : première classe du niveau sans se soucier de la série
        if (!$classe) {
            $classe = $classes->first(fn($c) => $c->niveau === $niveau);
        }

        return $classe?->id;
    }
}