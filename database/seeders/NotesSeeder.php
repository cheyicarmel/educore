<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotesSeeder extends Seeder
{
    public function run(): void
    {
        $anneeId   = 3;
        $semestre  = 1;
        $classeIds = [16,17,18,19,20,21,22,23,24,25,26,27,28,29,30];

        DB::table('notes')
            ->whereIn('inscription_id', function ($q) use ($classeIds, $anneeId) {
                $q->select('id')->from('inscriptions')
                  ->whereIn('classe_id', $classeIds)
                  ->where('annee_academique_id', $anneeId);
            })
            ->where('numero_semestre', $semestre)
            ->delete();

        $attributions = DB::table('attributions')
            ->where('annee_academique_id', $anneeId)
            ->whereIn('classe_id', $classeIds)
            ->get();

        $inscriptions = DB::table('inscriptions')
            ->whereIn('classe_id', $classeIds)
            ->where('annee_academique_id', $anneeId)
            ->get()
            ->groupBy('classe_id');

        $types = ['interrogation1', 'interrogation2', 'interrogation3', 'devoir1', 'devoir2'];

        $notes = [];

        foreach ($attributions as $attr) {
            $elevesClasse = $inscriptions[$attr->classe_id] ?? collect();

            foreach ($elevesClasse as $inscription) {
                $profil = $inscription->eleve_id % 10;

                [$min, $max] = match(true) {
                    $profil <= 1 => [15, 19],
                    $profil <= 3 => [12, 16],
                    $profil <= 6 => [8,  13],
                    $profil <= 8 => [4,  9],
                    default      => [2,  6],
                };

                foreach ($types as $type) {
                    $notes[] = [
                        'inscription_id'  => $inscription->id,
                        'matiere_id'      => $attr->matiere_id,
                        'enseignant_id'   => $attr->enseignant_id,
                        'type'            => $type,
                        'numero_semestre' => $semestre,
                        'valeur'          => $this->noteAleatoire($min, $max),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }
            }
        }

        foreach (array_chunk($notes, 500) as $chunk) {
            DB::table('notes')->insert($chunk);
        }

        $this->command->info(count($notes) . ' notes insérées avec succès.');
    }

    private function noteAleatoire(int $min, int $max): float
    {
        $entier = rand($min, $max);
        $demi   = rand(0, 1) ? 0.5 : 0.0;
        return min(20, $entier + $demi);
    }
}