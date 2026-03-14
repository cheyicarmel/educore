<?php
namespace App\Console\Commands;

use App\Models\Classe;
use App\Models\Inscription;
use App\Models\SuiviFinancier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PropagerFraisClasses extends Command
{
    protected $signature   = 'educore:propager-frais';
    protected $description = 'Propage les frais annuels des classes vers les inscriptions et crée les suivis financiers';

    public function handle()
    {
        $classes = Classe::where('frais_annuels', '>', 0)->with('inscriptions')->get();

        if ($classes->isEmpty()) {
            $this->error('Aucune classe avec des frais définis.');
            return;
        }

        $this->info('Propagation en cours...');
        $bar = $this->output->createProgressBar($classes->count());
        $bar->start();

        DB::transaction(function () use ($classes, $bar) {
            foreach ($classes as $classe) {
                foreach ($classe->inscriptions as $inscription) {

                    // Mettre à jour les frais de l'inscription
                    $inscription->update(['frais_annuels' => $classe->frais_annuels]);

                    // Créer le suivi financier
                    SuiviFinancier::create([
                        'inscription_id' => $inscription->id,
                        'total_du'       => $classe->frais_annuels,
                        'total_paye'     => 0,
                        'solde_restant'  => $classe->frais_annuels,
                        'statut'         => 'en_retard',
                    ]);
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Frais propagés et suivis financiers créés avec succès.');
    }
}