<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Note;
use App\Models\MoyenneMatiere;
use App\Models\MoyenneSemestre;
use App\Models\MoyenneAnnuelle;
use App\Models\Inscription;
use App\Models\Attribution;
use App\Models\CoefficientMatiere;

class CalculMoyennesSeeder extends Seeder
{
    public function run(): void
    {
        $anneeId   = 3;
        $classeIds = [16,17,18,19,20,21,22,23,24,25,26,27,28,29,30];

        // Classes premier cycle (moyenne simple sans coefficients)
        $premierCycleNiveaux = ['6ème', '5ème'];

        foreach ($classeIds as $classeId) {
            $classe = DB::table('classes')->where('id', $classeId)->first();

            $estPremierCycle = in_array($classe->niveau, $premierCycleNiveaux);

            $inscriptions = Inscription::where('classe_id', $classeId)
                ->where('annee_academique_id', $anneeId)
                ->get();

            $attributions = Attribution::where('classe_id', $classeId)
                ->where('annee_academique_id', $anneeId)
                ->get();

            $inscriptionIds = $inscriptions->pluck('id');

            // Calcul S1 et S2
            foreach ([1, 2] as $semestre) {

                // Vérifie si les moyennes sont déjà calculées pour cette classe/semestre
                $dejaCalcule = MoyenneSemestre::whereIn('inscription_id', $inscriptionIds)
                    ->where('numero_semestre', $semestre)
                    ->count();

                if ($dejaCalcule === $inscriptions->count() && $dejaCalcule > 0) {
                    $this->command->info("S{$semestre} déjà calculé pour {$classe->nom} — ignoré.");
                    continue;
                }

                $rangs = [];

                foreach ($inscriptions as $inscription) {
                    $moyennesMatiere = [];
                    $sommeCoeff      = 0;
                    $sommePonderee   = 0;

                    foreach ($attributions as $attr) {
                        $notes = Note::where('inscription_id', $inscription->id)
                            ->where('matiere_id', $attr->matiere_id)
                            ->where('numero_semestre', $semestre)
                            ->get()
                            ->keyBy('type');

                        if ($notes->count() < 5) continue;

                        $moyInterro = (
                            $notes['interrogation1']->valeur +
                            $notes['interrogation2']->valeur +
                            $notes['interrogation3']->valeur
                        ) / 3;

                        $moyGen = ($moyInterro + $notes['devoir1']->valeur + $notes['devoir2']->valeur) / 3;

                        if ($estPremierCycle) {
                            $moyAvecCoeff      = null;
                            $moyennesMatiere[] = $moyGen;
                        } else {
                            $coeff = CoefficientMatiere::where('matiere_id', $attr->matiere_id)
                                ->where('classe_id', $classeId)
                                ->first()?->coefficient ?? 1;
                            $moyAvecCoeff   = $moyGen * $coeff;
                            $sommeCoeff    += $coeff;
                            $sommePonderee += $moyAvecCoeff;
                        }

                        MoyenneMatiere::updateOrCreate(
                            [
                                'inscription_id'  => $inscription->id,
                                'matiere_id'      => $attr->matiere_id,
                                'numero_semestre' => $semestre,
                            ],
                            [
                                'moyenne_interrogations'   => round($moyInterro, 2),
                                'moyenne_generale'         => round($moyGen, 2),
                                'moyenne_avec_coefficient' => $moyAvecCoeff ? round($moyAvecCoeff, 2) : null,
                            ]
                        );
                    }

                    if ($estPremierCycle && count($moyennesMatiere) > 0) {
                        $moyenneSemestre = array_sum($moyennesMatiere) / count($moyennesMatiere);
                    } elseif (!$estPremierCycle && $sommeCoeff > 0) {
                        $moyenneSemestre = $sommePonderee / $sommeCoeff;
                    } else {
                        continue;
                    }

                    MoyenneSemestre::updateOrCreate(
                        ['inscription_id' => $inscription->id, 'numero_semestre' => $semestre],
                        ['valeur' => round($moyenneSemestre, 2)]
                    );

                    $rangs[$inscription->id] = $moyenneSemestre;
                }

                // Classement
                arsort($rangs);
                $rang = 1;
                foreach ($rangs as $inscriptionId => $valeur) {
                    MoyenneSemestre::where('inscription_id', $inscriptionId)
                        ->where('numero_semestre', $semestre)
                        ->update(['rang' => $rang++]);
                }

                $this->command->info("S{$semestre} calculé pour {$classe->nom} — {$inscriptions->count()} élèves.");
            }

            // Calcul annuel
            $dejaCalculeAnnuel = MoyenneAnnuelle::whereIn('inscription_id', $inscriptionIds)->count();

            if ($dejaCalculeAnnuel === $inscriptions->count() && $dejaCalculeAnnuel > 0) {
                $this->command->info("Annuel déjà calculé pour {$classe->nom} — ignoré.");
                continue;
            }

            $rangsAnnuels = [];

            foreach ($inscriptions as $inscription) {
                $s1 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 1)->first();
                $s2 = MoyenneSemestre::where('inscription_id', $inscription->id)->where('numero_semestre', 2)->first();

                if (!$s1 || !$s2) continue;

                $valeur = (($s2->valeur * 2) + $s1->valeur) / 3;

                MoyenneAnnuelle::updateOrCreate(
                    ['inscription_id' => $inscription->id],
                    [
                        'valeur'   => round($valeur, 2),
                        'decision' => $valeur >= 10 ? 'passant' : 'doublant',
                    ]
                );

                $rangsAnnuels[$inscription->id] = $valeur;
            }

            // Classement annuel
            arsort($rangsAnnuels);
            $rang = 1;
            foreach ($rangsAnnuels as $inscriptionId => $valeur) {
                MoyenneAnnuelle::where('inscription_id', $inscriptionId)
                    ->update(['rang' => $rang++]);
            }

            $this->command->info("Annuel calculé pour {$classe->nom}.");
        }

        $this->command->info('Tous les calculs sont terminés.');
    }
}