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
        '6ème' => '5ème',
        '5ème' => '4ème',
        '4ème' => '3ème',
        '3ème' => '2nde',
        '2nde' => '1ère',
        '1ère' => 'Tle',
        'Tle'  => null,
    ];

    public function index()
    {
        $anneeActive     = AnneeAcademique::active()->first();
        $anneesSuivantes = AnneeAcademique::where('statut', 'inactive')
            ->orderBy('created_at', 'desc')
            ->get();

        // Vérifier si toutes les moyennes annuelles sont calculées
        $totalInscriptions = Inscription::where('annee_academique_id', $anneeActive?->id)->count();
        $totalMoyennes     = MoyenneAnnuelle::whereHas('inscription', fn($q) =>
            $q->where('annee_academique_id', $anneeActive?->id)
        )->count();
        $pret = $totalInscriptions > 0 && $totalMoyennes >= $totalInscriptions;

        return view('admin.passage.index', compact(
            'anneeActive', 'anneesSuivantes', 'totalInscriptions', 'totalMoyennes', 'pret'
        ));
    }

    public function traiter(Request $request)
    {
        $request->validate([
            'annee_suivante_id' => 'required|exists:annees_academiques,id',
        ]);

        $anneeActive   = AnneeAcademique::active()->first();
        $anneeSuivante = AnneeAcademique::findOrFail($request->annee_suivante_id);

        // Classes de la nouvelle année
        $classesSuivantes = Classe::where('annee_academique_id', $anneeSuivante->id)
            ->with('serie')
            ->get();

        if ($classesSuivantes->isEmpty()) {
            return back()->with('error', 'Aucune classe n\'est définie pour l\'année suivante. Veuillez les créer d\'abord.');
        }

        DB::transaction(function () use ($anneeActive, $anneeSuivante, $classesSuivantes) {

            $inscriptions = Inscription::where('annee_academique_id', $anneeActive->id)
                ->with(['eleve', 'classe.serie', 'moyenneAnnuelle'])
                ->get();

            foreach ($inscriptions as $inscription) {
                $moyenneAnnuelle = $inscription->moyenneAnnuelle;
                if (!$moyenneAnnuelle) continue;

                $decision      = $moyenneAnnuelle->decision;
                $classeActuelle = $inscription->classe;
                $niveauBase    = $this->getNiveauBase($classeActuelle->niveau);
                $serieActuelle = $classeActuelle->serie?->code;

                if ($decision === 'passant') {
                    $niveauSuivant = self::PROGRESSION[$niveauBase] ?? null;

                    // Terminale passant → diplômé, on ne réinscrit pas
                    if ($niveauSuivant === null) continue;

                    $serieCalculee = $this->calculerSerie($inscription, $niveauBase, $serieActuelle);
                    $classeId      = $this->trouverClasseId($classesSuivantes, $niveauSuivant, $serieCalculee);

                } else {
                    // Doublant → même classe dans la nouvelle année
                    $classeId = $this->trouverClasseId($classesSuivantes, $niveauBase, $serieActuelle);
                }

                if (!$classeId) continue;

                // Créer la nouvelle inscription
                $nouvelleInscription = Inscription::create([
                    'eleve_id'            => $inscription->eleve_id,
                    'classe_id'           => $classeId,
                    'annee_academique_id' => $anneeSuivante->id,
                    'statut'              => 'actif',
                    'frais_annuels'       => $inscription->frais_annuels,
                ]);

                // Créer le suivi financier vide
                SuiviFinancier::create([
                    'inscription_id' => $nouvelleInscription->id,
                    'total_du'       => $inscription->frais_annuels,
                    'total_paye'     => 0,
                    'solde_restant'  => $inscription->frais_annuels,
                    'statut'         => 'en_retard',
                ]);
            }

            // Changer statuts années
            $anneeActive->update(['statut' => 'terminee']);
            $anneeSuivante->update(['statut' => 'active']);
        });

        return redirect()->route('admin.passage.index')
            ->with('success', 'Passage effectué avec succès. La nouvelle année académique est maintenant active.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function getNiveauBase(string $niveau): string
    {
        return trim(preg_replace('/\s+[A-Z]$/', '', $niveau));
    }

    private function calculerSerie(Inscription $inscription, string $niveauActuel, ?string $serieActuelle): string
    {
        if (in_array($niveauActuel, ['5ème', '4ème'])) {
            return $this->calculerSerieLC($inscription);
        }

        if ($niveauActuel === '3ème') {
            if ($serieActuelle === 'L') return 'A';
            return $this->calculerSerieSecondCycle($inscription);
        }

        if (in_array($niveauActuel, ['2nde', '1ère'])) {
            if ($serieActuelle === 'A') return 'A';
            return $this->calculerSerieSecondCycle($inscription);
        }

        return $serieActuelle ?? 'A';
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
        $classe = $classes->first(function ($c) use ($niveau, $serie) {
            if ($this->getNiveauBase($c->niveau) !== $niveau) return false;
            if ($serie) return $c->serie?->code === $serie;
            return !$c->serie_id;
        });

        if (!$classe) {
            $classe = $classes->first(fn($c) => $this->getNiveauBase($c->niveau) === $niveau);
        }

        return $classe?->id;
    }
}