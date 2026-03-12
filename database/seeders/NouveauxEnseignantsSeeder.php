<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NouveauxEnseignantsSeeder extends Seeder
{
    public function run(): void
    {
        // Enseignant 1 — Maths — PP Tle C
        $userId1 = DB::table('users')->insertGetId([
            'prenom'     => 'Gérard',
            'nom'        => 'ALOHOU',
            'email'      => 'g.alohou@educore.bj',
            'password'   => Hash::make('password123'),
            'role'       => 'enseignant',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('enseignants')->insert([
            'user_id'     => $userId1,
            'specialite'  => 'Mathématiques',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Enseignant 2 — Physique-Chimie — PP Tle D
        $userId2 = DB::table('users')->insertGetId([
            'prenom'     => 'Rodrigue',
            'nom'        => 'HOUNSA',
            'email'      => 'r.hounsa@educore.bj',
            'password'   => Hash::make('password123'),
            'role'       => 'enseignant',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('enseignants')->insert([
            'user_id'     => $userId2,
            'specialite'  => 'Physique-Chimie',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->command->info('2 nouveaux enseignants créés avec succès.');
    }
}