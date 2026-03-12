<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttributionsEtCoefficientsSeeder extends Seeder
{
    public function run(): void
    {
        $anneeId = 3;
        $date    = Carbon::now()->toDateString();

        DB::table('attributions')->where('annee_academique_id', $anneeId)->delete();
        DB::table('coefficient_matieres')->whereIn('classe_id', [16,17,18,19,20,21,22,23,24,25,26,27,28,29,30])->delete();

        // ENSEIGNANTS
        // 2  John ODJO           — Maths        — PP 6ème
        // 3  Dylan AGBO          — SVT          — PP 1ère D
        // 4  Diane KEKOU         — Philosophie  — PP 2nde C
        // 5  Chance KOUKPAKI     — Français     — PP 4ème L
        // 6  Klaus AZOVE         — Anglais      — PP 2nde D
        // 7  Kossi AGBeKO        — Maths        — PP 4ème C
        // 8  Dodji Tossou        — Physique-Chimie — PP 3ème C
        // 9  Kossivi Mensah      — SVT          — PP 1ère C
        // 10 Honoré Gbaguidi     — Français     — PP 5ème
        // 11 Céleste Assogba     — Anglais      — PP 1ère A
        // 12 Bernadette Houenou  — Histoire-Géo — PP Tle A
        // 13 Florent Dossou      — Philosophie  — PP 2nde A
        // 16 Rosine Fassinou     — Espagnol     — PP 3ème L
        // 17 Gérard ALOHOU       — Maths        — PP Tle C
        // 18 Rodrigue HOUNSA     — Physique-Chimie — PP Tle D

        // [classe_id, enseignant_id, matiere_id, est_prof_principal]
        $attributions = [

            // ── 6ème (16) — Maths, PC, Français, Anglais, SVT, HG
            // PP : John ODJO (Maths)
            [16, 2,  1, true],
            [16, 8,  2, false],
            [16, 5,  4, false],
            [16, 11, 6, false],
            [16, 3,  3, false],
            [16, 12, 5, false],

            // ── 5ème (17) — Maths, PC, Français, Anglais, SVT, HG
            // PP : Honoré Gbaguidi (Français)
            [17, 7,  1, false],
            [17, 8,  2, false],
            [17, 10, 4, true],
            [17, 6,  6, false],
            [17, 9,  3, false],
            [17, 12, 5, false],

            // ── 4ème C (18) — Maths, PC, Français, Anglais, SVT, HG
            // PP : Kossi AGBeKO (Maths)
            [18, 7,  1, true],
            [18, 8,  2, false],
            [18, 10, 4, false],
            [18, 11, 6, false],
            [18, 9,  3, false],
            [18, 12, 5, false],

            // ── 4ème L (19) — Maths, Français, Anglais, SVT, HG, Espagnol
            // PP : Chance KOUKPAKI (Français)
            [19, 2,  1, false],
            [19, 5,  4, true],
            [19, 6,  6, false],
            [19, 9,  3, false],
            [19, 12, 5, false],
            [19, 16, 8, false],

            // ── 3ème C (20) — Maths, PC, Français, Anglais, SVT, HG
            // PP : Dodji Tossou (Physique-Chimie)
            [20, 2,  1, false],
            [20, 8,  2, true],
            [20, 5,  4, false],
            [20, 11, 6, false],
            [20, 3,  3, false],
            [20, 12, 5, false],

            // ── 3ème L (21) — Maths, Français, Anglais, SVT, HG, Espagnol
            // PP : Rosine Fassinou (Espagnol)
            [21, 7,  1, false],
            [21, 10, 4, false],
            [21, 6,  6, false],
            [21, 9,  3, false],
            [21, 12, 5, false],
            [21, 16, 8, true],

            // ── 2nde A (25) — Maths, Français, Anglais, HG, Philo, Espagnol
            // PP : Florent Dossou (Philo)
            [25, 7,  1, false],
            [25, 10, 4, false],
            [25, 11, 6, false],
            [25, 12, 5, false],
            [25, 13, 7, true],
            [25, 16, 8, false],

            // ── 2nde C (27) — Maths, PC, SVT, Français, Anglais, HG, Philo
            // PP : Diane KEKOU (Philo)
            [27, 2,  1, false],
            [27, 8,  2, false],
            [27, 9,  3, false],
            [27, 10, 4, false],
            [27, 12, 5, false],
            [27, 11, 6, false],
            [27, 4,  7, true],

            // ── 2nde D (26) — Maths, PC, SVT, Français, Anglais, HG, Philo
            // PP : Klaus AZOVE (Anglais)
            [26, 7,  1, false],
            [26, 8,  2, false],
            [26, 3,  3, false],
            [26, 5,  4, false],
            [26, 12, 5, false],
            [26, 6,  6, true],
            [26, 13, 7, false],

            // ── 1ère A (22) — Maths, Français, Anglais, HG, Philo, Espagnol
            // PP : Céleste Assogba (Anglais)
            [22, 2,  1, false],
            [22, 5,  4, false],
            [22, 11, 6, true],
            [22, 12, 5, false],
            [22, 4,  7, false],
            [22, 16, 8, false],

            // ── 1ère C (24) — Maths, PC, SVT, Français, Anglais, HG, Philo
            // PP : Kossivi Mensah (SVT)
            [24, 7,  1, false],
            [24, 8,  2, false],
            [24, 9,  3, true],
            [24, 5,  4, false],
            [24, 12, 5, false],
            [24, 11, 6, false],
            [24, 4,  7, false],

            // ── 1ère D (23) — Maths, PC, SVT, Français, Anglais, HG, Philo
            // PP : Dylan AGBO (SVT)
            [23, 2,  1, false],
            [23, 8,  2, false],
            [23, 3,  3, true],
            [23, 10, 4, false],
            [23, 12, 5, false],
            [23, 11, 6, false],
            [23, 4,  7, false],

            // ── Tle A (28) — Maths, Français, Anglais, HG, Philo, Espagnol
            // PP : Bernadette Houenou (HG)
            [28, 7,  1, false],
            [28, 10, 4, false],
            [28, 11, 6, false],
            [28, 12, 5, true],
            [28, 4,  7, false],
            [28, 16, 8, false],

            // ── Tle C (29) — Maths, PC, SVT, Français, Anglais, HG, Philo
            // PP : Gérard ALOHOU (Maths)
            [29, 17, 1, true],
            [29, 8,  2, false],
            [29, 3,  3, false],
            [29, 5,  4, false],
            [29, 12, 5, false],
            [29, 6,  6, false],
            [29, 4,  7, false],

            // ── Tle D (30) — Maths, PC, SVT, Français, Anglais, HG, Philo
            // PP : Rodrigue HOUNSA (Physique-Chimie)
            [30, 2,  1, false],
            [30, 18, 2, true],
            [30, 9,  3, false],
            [30, 10, 4, false],
            [30, 12, 5, false],
            [30, 11, 6, false],
            [30, 13, 7, false],
        ];

        foreach ($attributions as [$classeId, $enseignantId, $matiereId, $estPP]) {
            DB::table('attributions')->insert([
                'enseignant_id'       => $enseignantId,
                'classe_id'           => $classeId,
                'matiere_id'          => $matiereId,
                'annee_academique_id' => $anneeId,
                'est_prof_principal'  => $estPP,
                'date_attribution'    => $date,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        // ── COEFFICIENTS
        $coefficients = [

            // 6ème, 5ème — coef 1 partout (moyenne simple)
            ...$this->coeffsPourClasses([16, 17], [
                1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1,
            ]),

            // 4ème C, 3ème C
            ...$this->coeffsPourClasses([18, 20], [
                1 => 2, 2 => 2, 3 => 2, 4 => 2, 5 => 1, 6 => 1,
            ]),

            // 4ème L, 3ème L
            ...$this->coeffsPourClasses([19, 21], [
                1 => 1, 3 => 1, 4 => 3, 5 => 2, 6 => 2, 8 => 2,
            ]),

            // 2nde C, 2nde D
            ...$this->coeffsPourClasses([27, 26], [
                1 => 4, 2 => 3, 3 => 3, 4 => 3, 5 => 2, 6 => 2, 7 => 1,
            ]),

            // 2nde A
            ...$this->coeffsPourClasses([25], [
                1 => 2, 4 => 4, 5 => 3, 6 => 3, 7 => 2, 8 => 2,
            ]),

            // 1ère C, 1ère D
            ...$this->coeffsPourClasses([24, 23], [
                1 => 5, 2 => 4, 3 => 4, 4 => 3, 5 => 2, 6 => 2, 7 => 1,
            ]),

            // 1ère A
            ...$this->coeffsPourClasses([22], [
                1 => 2, 4 => 4, 5 => 3, 6 => 3, 7 => 3, 8 => 1,
            ]),

            // Tle C, Tle D
            ...$this->coeffsPourClasses([29, 30], [
                1 => 5, 2 => 5, 3 => 4, 4 => 3, 5 => 2, 6 => 2, 7 => 1,
            ]),

            // Tle A
            ...$this->coeffsPourClasses([28], [
                1 => 2, 4 => 4, 5 => 3, 6 => 3, 7 => 3, 8 => 1,
            ]),
        ];

        foreach ($coefficients as $coeff) {
            DB::table('coefficient_matieres')->insert([
                'classe_id'   => $coeff[0],
                'matiere_id'  => $coeff[1],
                'coefficient' => $coeff[2],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('Attributions et coefficients insérés avec succès.');
    }

    private function coeffsPourClasses(array $classes, array $matiereCoeffs): array
    {
        $result = [];
        foreach ($classes as $classeId) {
            foreach ($matiereCoeffs as $matiereId => $coeff) {
                $result[] = [$classeId, $matiereId, $coeff];
            }
        }
        return $result;
    }
}