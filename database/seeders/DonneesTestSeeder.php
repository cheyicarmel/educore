<?php

namespace Database\Seeders;

use App\Models\AnneeAcademique;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\Enseignant;
use App\Models\Inscription;
use App\Models\SuiviFinancier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DonneesTestSeeder extends Seeder
{
    // Noms et prénoms béninois
    private array $noms = [
        'Agbeko', 'Ahomégnon', 'Akakpo', 'Aklassou', 'Alabi', 'Amavi', 'Amégbor',
        'Assogba', 'Atanasso', 'Atrokpo', 'Avlessi', 'Ayivodji', 'Azonhiho',
        'Dédéou', 'Djossou', 'Dohou', 'Dossou', 'Fassinou', 'Gbaguidi', 'Gbéto',
        'Gnansounou', 'Houenou', 'Hounsou', 'Kpanou', 'Kpèdékpo', 'Laleye',
        'Lokossou', 'Mensah', 'Mitchaï', 'Montcho', 'Noudéhou', 'Ogoubiyi',
        'Padonou', 'Sagbo', 'Sèmassou', 'Soglo', 'Sonon', 'Talon', 'Tchegnon',
        'Tossou', 'Totin', 'Vigan', 'Vissoh', 'Yèhouénou', 'Zannou', 'Zinsou',
    ];

    private array $prenomsM = [
        'Adjoua', 'Agossou', 'Ahouan', 'Akpédjé', 'Alidou', 'Amoussou', 'Anselme',
        'Bénédict', 'Blaise', 'Boris', 'Calixte', 'Charly', 'Clovis', 'Codjo',
        'Cyrille', 'Damien', 'Darius', 'Dodji', 'Edem', 'Edgard', 'Emile',
        'Euloge', 'Fabrice', 'Félicien', 'Florent', 'Frédéric', 'Gilles',
        'Hervé', 'Hilaire', 'Hyacinthe', 'Innocent', 'Jonas', 'Joël', 'Jules',
        'Kokou', 'Kossivi', 'Léon', 'Lionel', 'Luc', 'Marius', 'Mathieu',
        'Maxime', 'Médard', 'Narcisse', 'Noël', 'Norbert', 'Octave', 'Pacôme',
        'Pascal', 'Patrice', 'Philippe', 'Pierre', 'Prosper', 'Rodrigue',
        'Roland', 'Romain', 'Sébastien', 'Serge', 'Théodore', 'Thierry',
        'Timothée', 'Valentin', 'Victor', 'Wilfried', 'Xavier', 'Yao', 'Yves',
    ];

    private array $prenomsF = [
        'Abosède', 'Adélaïde', 'Adèle', 'Afiavi', 'Agnès', 'Aïcha', 'Aïssatou',
        'Albertine', 'Amélie', 'Anastasie', 'Angèle', 'Anita', 'Armelle',
        'Béatrice', 'Bénédicte', 'Bernadette', 'Brigitte', 'Carole', 'Céleste',
        'Chantal', 'Christine', 'Christiane', 'Claire', 'Claudine', 'Corine',
        'Delphine', 'Denise', 'Désirée', 'Edwige', 'Elise', 'Emilienne',
        'Eudoxie', 'Eulalie', 'Evariste', 'Fatoumata', 'Félicité', 'Florence',
        'Françoise', 'Geneviève', 'Georgette', 'Germaine', 'Ghislaine',
        'Honorine', 'Hortense', 'Irène', 'Joëlle', 'Joséphine', 'Julie',
        'Laure', 'Laurence', 'Léa', 'Léonie', 'Lucie', 'Madeleine', 'Marie',
        'Marthe', 'Mathilde', 'Monique', 'Nadège', 'Nathalie', 'Nicole',
        'Odile', 'Pascaline', 'Patricia', 'Pauline', 'Rachelle', 'Regina',
        'Rosine', 'Sandrine', 'Séraphine', 'Solange', 'Sophie', 'Sylvie',
        'Thérèse', 'Valentine', 'Véronique', 'Viviane', 'Yvonne', 'Zélie',
    ];

    // Matières avec l'ID tel qu'en base
    // Format : ['nom_partiel' => id] — on cherchera par nom
    private array $enseignantsData = [
        ['prenom' => 'Kossi',     'nom' => 'Agbéko',    'specialite' => 'Mathématiques'],
        ['prenom' => 'Dodji',     'nom' => 'Tossou',    'specialite' => 'Physique-Chimie'],
        ['prenom' => 'Kossivi',   'nom' => 'Mensah',    'specialite' => 'SVT'],
        ['prenom' => 'Honoré',    'nom' => 'Gbaguidi',  'specialite' => 'Français'],
        ['prenom' => 'Céleste',   'nom' => 'Assogba',   'specialite' => 'Anglais'],
        ['prenom' => 'Bernadette','nom' => 'Houenou',   'specialite' => 'Histoire-Géographie'],
        ['prenom' => 'Florent',   'nom' => 'Dossou',    'specialite' => 'Philosophie'],
        ['prenom' => 'Martine',   'nom' => 'Zannou',    'specialite' => 'Économie'],
        ['prenom' => 'Théodore',  'nom' => 'Akakpo',    'specialite' => 'Allemand'],
        ['prenom' => 'Rosine',    'nom' => 'Fassinou',  'specialite' => 'Espagnol'],
    ];

    public function run(): void
    {
        $anneeActive = AnneeAcademique::where('statut', 'active')->first();

        if (!$anneeActive) {
            $this->command->error('Aucune année académique active trouvée. Impossible de lancer le seeder.');
            return;
        }

        // Classes de l'année active uniquement (IDs 1-15)
        $classes = Classe::where('annee_academique_id', $anneeActive->id)->get();

        if ($classes->isEmpty()) {
            $this->command->error('Aucune classe trouvée pour l\'année active.');
            return;
        }

        $this->command->info("Année active : {$anneeActive->libelle}");
        $this->command->info("{$classes->count()} classes trouvées.");

        // ─── Enseignants ───────────────────────────────────────────────
        $this->command->info('Création des enseignants...');
        $compteurEnseignant = 0;

        foreach ($this->enseignantsData as $data) {
            $email = strtolower(
                $this->removeAccents($data['prenom']) . '.' .
                $this->removeAccents($data['nom']) .
                '@educore-test.com'
            );

            // Éviter les doublons
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $user = User::create([
                'nom'       => $data['nom'],
                'prenom'    => $data['prenom'],
                'email'     => $email,
                'password'  => Hash::make('password123'),
                'role'      => 'enseignant',
                'est_actif' => true,
            ]);

            Enseignant::create([
                'user_id'    => $user->id,
                'specialite' => $data['specialite'],
                'telephone'  => '+229 9' . rand(1000000, 9999999),
            ]);

            $compteurEnseignant++;
        }

        $this->command->info("{$compteurEnseignant} enseignants créés.");

        // ─── Élèves ────────────────────────────────────────────────────
        $this->command->info('Création des élèves...');
        $compteurEleve    = 0;
        $annee            = date('Y');
        $numeroActuel     = \App\Models\Eleve::where('numero_matricule', 'like', "EDC-{$annee}-%")
                                ->count() + 1;

        $fraísParNiveau = [
            '6ème'   => 120000,
            '5ème'   => 120000,
            '4ème C' => 135000,
            '4ème L' => 135000,
            '3ème C' => 135000,
            '3ème L' => 135000,
            '2nde A' => 150000,
            '2nde C' => 150000,
            '2nde D' => 150000,
            '1ère A' => 165000,
            '1ère C' => 165000,
            '1ère D' => 165000,
            'Tle A'  => 180000,
            'Tle C'  => 180000,
            'Tle D'  => 180000,
        ];

        foreach ($classes as $classe) {
            for ($i = 0; $i < 20; $i++) {
                $sexe    = rand(0, 1) ? 'M' : 'F';
                $prenom  = $sexe === 'M'
                    ? $this->prenomsM[array_rand($this->prenomsM)]
                    : $this->prenomsF[array_rand($this->prenomsF)];
                $nom     = $this->noms[array_rand($this->noms)];

                $baseEmail = strtolower(
                    $this->removeAccents($prenom) . '.' .
                    $this->removeAccents($nom)
                );
                $email = $baseEmail . rand(100, 999) . '@parent-test.com';

                // S'assurer que l'email est unique
                while (User::where('email', $email)->exists()) {
                    $email = $baseEmail . rand(1000, 9999) . '@parent-test.com';
                }

                $matricule = "EDC-{$annee}-" . str_pad($numeroActuel, 3, '0', STR_PAD_LEFT);
                $numeroActuel++;

                $frais = $fraísParNiveau[$classe->nom] ?? 150000;

                $user = User::create([
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'email'     => $email,
                    'password'  => Hash::make('password123'),
                    'role'      => 'eleve',
                    'est_actif' => true,
                ]);

                $anneeNaissance = rand(2005, 2014);
                $eleve = Eleve::create([
                    'user_id'          => $user->id,
                    'numero_matricule' => $matricule,
                    'date_naissance'   => $anneeNaissance . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'sexe'             => $sexe,
                    'email_parent'     => $email,
                    'telephone_parent' => '+229 9' . rand(1000000, 9999999),
                ]);

                $inscription = Inscription::create([
                    'eleve_id'            => $eleve->id,
                    'classe_id'           => $classe->id,
                    'annee_academique_id' => $anneeActive->id,
                    'statut'              => 'actif',
                    'frais_annuels'       => $frais,
                ]);

                SuiviFinancier::create([
                    'inscription_id' => $inscription->id,
                    'total_du'       => $frais,
                    'total_paye'     => 0,
                    'solde_restant'  => $frais,
                    'statut'         => 'en_retard',
                ]);

                $compteurEleve++;
            }
        }

        $this->command->info("{$compteurEleve} élèves créés.");
        $this->command->info('Seeder terminé avec succès !');
        $this->command->info('Mot de passe pour tous les comptes créés : password123');
    }

    private function removeAccents(string $str): string
    {
        $search  = ['à','á','â','ã','ä','å','è','é','ê','ë','ì','í','î','ï','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ñ','ç','À','Á','Â','Ã','Ä','Å','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','Ñ','Ç'];
        $replace = ['a','a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','y','n','c','A','A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','N','C'];
        return str_replace($search, $replace, $str);
    }
}