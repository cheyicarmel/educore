<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Comptable;

// Page de connexion
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

// Traitement du formulaire de connexion
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

// Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Admin et Superadmin
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Années académiques
    Route::get('/admin/annees', [App\Http\Controllers\Admin\AnneeAcademiqueController::class, 'index'])->name('admin.annees.index');
    Route::post('/admin/annees', [App\Http\Controllers\Admin\AnneeAcademiqueController::class, 'store'])->name('admin.annees.store');
    Route::put('/admin/annees/{anneeAcademique}', [App\Http\Controllers\Admin\AnneeAcademiqueController::class, 'update'])->name('admin.annees.update');
    Route::patch('/admin/annees/{anneeAcademique}/status', [App\Http\Controllers\Admin\AnneeAcademiqueController::class, 'toggleStatus'])->name('admin.annees.status');
    Route::delete('/admin/annees/{anneeAcademique}', [App\Http\Controllers\Admin\AnneeAcademiqueController::class, 'destroy'])->name('admin.annees.destroy');

    // Séries
    Route::get('/admin/series', [App\Http\Controllers\Admin\SerieController::class, 'index'])->name('admin.series.index');
    Route::post('/admin/series', [App\Http\Controllers\Admin\SerieController::class, 'store'])->name('admin.series.store');
    Route::put('/admin/series/{serie}', [App\Http\Controllers\Admin\SerieController::class, 'update'])->name('admin.series.update');
    Route::delete('/admin/series/{serie}', [App\Http\Controllers\Admin\SerieController::class, 'destroy'])->name('admin.series.destroy');

    // Classes
    Route::get('/admin/classes', [App\Http\Controllers\Admin\ClasseController::class, 'index'])->name('admin.classes.index');
    Route::post('/admin/classes', [App\Http\Controllers\Admin\ClasseController::class, 'store'])->name('admin.classes.store');
    Route::put('/admin/classes/{classe}', [App\Http\Controllers\Admin\ClasseController::class, 'update'])->name('admin.classes.update');
    Route::delete('/admin/classes/{classe}', [App\Http\Controllers\Admin\ClasseController::class, 'destroy'])->name('admin.classes.destroy');

    // Matières
    Route::get('/admin/matieres', [App\Http\Controllers\Admin\MatiereController::class, 'index'])->name('admin.matieres.index');
    Route::post('/admin/matieres', [App\Http\Controllers\Admin\MatiereController::class, 'store'])->name('admin.matieres.store');
    Route::put('/admin/matieres/{matiere}', [App\Http\Controllers\Admin\MatiereController::class, 'update'])->name('admin.matieres.update');
    Route::delete('/admin/matieres/{matiere}', [App\Http\Controllers\Admin\MatiereController::class, 'destroy'])->name('admin.matieres.destroy');
    Route::post('/admin/matieres/{matiere}/coefficients', [App\Http\Controllers\Admin\MatiereController::class, 'updateCoefficients'])->name('admin.matieres.coefficients');

    // Enseignants
    Route::get('/admin/enseignants', [App\Http\Controllers\Admin\EnseignantController::class, 'index'])->name('admin.enseignants.index');
    Route::post('/admin/enseignants', [App\Http\Controllers\Admin\EnseignantController::class, 'store'])->name('admin.enseignants.store');
    Route::put('/admin/enseignants/{enseignant}', [App\Http\Controllers\Admin\EnseignantController::class, 'update'])->name('admin.enseignants.update');
    Route::patch('/admin/enseignants/{enseignant}/toggle', [App\Http\Controllers\Admin\EnseignantController::class, 'toggleStatus'])->name('admin.enseignants.toggle');
    Route::delete('/admin/enseignants/{enseignant}', [App\Http\Controllers\Admin\EnseignantController::class, 'destroy'])->name('admin.enseignants.destroy');

    // Élèves
    Route::get('/admin/eleves', [App\Http\Controllers\Admin\EleveController::class, 'index'])->name('admin.eleves.index');
    Route::post('/admin/eleves', [App\Http\Controllers\Admin\EleveController::class, 'store'])->name('admin.eleves.store');
    Route::put('/admin/eleves/{eleve}', [App\Http\Controllers\Admin\EleveController::class, 'update'])->name('admin.eleves.update');
    Route::delete('/admin/eleves/{eleve}', [App\Http\Controllers\Admin\EleveController::class, 'destroy'])->name('admin.eleves.destroy');

    // Attributions
    Route::get('/admin/attributions', [App\Http\Controllers\Admin\AttributionController::class, 'index'])->name('admin.attributions.index');
    Route::post('/admin/attributions', [App\Http\Controllers\Admin\AttributionController::class, 'store'])->name('admin.attributions.store');
    Route::delete('/admin/attributions/{attribution}', [App\Http\Controllers\Admin\AttributionController::class, 'destroy'])->name('admin.attributions.destroy');

    // Finances
    Route::get('/admin/finances', [App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finances.index');

    // Administrateurs
    Route::get('/admin/administrateurs', [App\Http\Controllers\Admin\AdministrateurController::class, 'index'])->name('admin.administrateurs.index');
    Route::post('/admin/administrateurs', [App\Http\Controllers\Admin\AdministrateurController::class, 'store'])->name('admin.administrateurs.store');
    Route::put('/admin/administrateurs/{user}', [App\Http\Controllers\Admin\AdministrateurController::class, 'update'])->name('admin.administrateurs.update');
    Route::patch('/admin/administrateurs/{user}/toggle', [App\Http\Controllers\Admin\AdministrateurController::class, 'toggleStatus'])->name('admin.administrateurs.toggle');
    Route::delete('/admin/administrateurs/{user}', [App\Http\Controllers\Admin\AdministrateurController::class, 'destroy'])->name('admin.administrateurs.destroy');

    // Paramètres
    Route::get('/admin/parametres',  [App\Http\Controllers\Admin\ParametreController::class, 'index'])->name('admin.parametres.index');
    Route::put('/admin/parametres',  [App\Http\Controllers\Admin\ParametreController::class, 'update'])->name('admin.parametres.update');
});




//  Enseignant
Route::middleware(['auth', 'role:enseignant'])->group(function () {

    // Dashboard
    Route::get('/enseignant/dashboard', [App\Http\Controllers\Enseignant\DashboardController::class, 'index'])->name('enseignant.dashboard');
    
    // Les Classes
    Route::get('/enseignant/classes', [App\Http\Controllers\Enseignant\ClasseController::class, 'index'])->name('enseignant.classes.index');
        // Saisir des notes depui de la liste des classes
        Route::get('/enseignant/notes',  [App\Http\Controllers\Enseignant\NoteController::class, 'index'])->name('enseignant.notes.index');
        Route::post('/enseignant/notes', [App\Http\Controllers\Enseignant\NoteController::class, 'store'])->name('enseignant.notes.store');
        Route::post('/enseignant/notes/valider-moyennes', [App\Http\Controllers\Enseignant\NoteController::class, 'validerMoyennes'])->name('enseignant.notes.valider-moyennes');
    
    // Classe principale
    Route::get('/enseignant/ma-classe',              [App\Http\Controllers\Enseignant\MaClassePrincipaleController::class, 'index'])->name('enseignant.ma-classe');
    Route::post('/enseignant/ma-classe/calculer',    [App\Http\Controllers\Enseignant\MaClassePrincipaleController::class, 'calculerMoyennes'])->name('enseignant.ma-classe.calculer-moyennes');
    Route::post('/enseignant/ma-classe/releve',      [App\Http\Controllers\Enseignant\MaClassePrincipaleController::class, 'genererReleve'])->name('enseignant.ma-classe.generer-releve');

    // Profil
    Route::get('/enseignant/profil', [App\Http\Controllers\Enseignant\ProfilController::class, 'index'])->name('enseignant.profil');
    Route::put('/enseignant/profil', [App\Http\Controllers\Enseignant\ProfilController::class, 'update'])->name('enseignant.profil.update');
});




// Élève

Route::middleware(['auth', 'role:eleve'])->group(function () {
    // Dashboard
    Route::get('/eleve/dashboard', [App\Http\Controllers\Eleve\DashboardController::class, 'index'])->name('eleve.dashboard');

    // Notes
    Route::get('/eleve/notes', [App\Http\Controllers\Eleve\NotesController::class, 'index'])->name('eleve.notes');

    // Bulletins
    Route::get('/eleve/bulletins', [App\Http\Controllers\Eleve\BulletinsController::class, 'index'])->name('eleve.bulletins');
    Route::get('/eleve/bulletins/{id}/download', fn($id) => back())->name('eleve.bulletins.download');
    
    // Profil
    Route::get('/eleve/profil',  [App\Http\Controllers\Eleve\ProfilController::class, 'index'])->name('eleve.profil');
    Route::put('/eleve/profil',  [App\Http\Controllers\Eleve\ProfilController::class, 'update'])->name('eleve.profil.update');
});




// Comptable

Route::middleware(['auth', 'role:comptable'])->group(function () {

    // Dashboard
    Route::get('/comptable/dashboard', [App\Http\Controllers\Comptable\DashboardController::class, 'index'])->name('comptable.dashboard');
    
    // Enregistrement des paiements et generation des reçus
    Route::get('/comptable/paiements/creer',  [App\Http\Controllers\Comptable\PaiementController::class, 'create'])->name('comptable.paiements.create');
    Route::get('/comptable/paiements/search', [App\Http\Controllers\Comptable\PaiementController::class, 'search'])->name('comptable.paiements.search');
    Route::post('/comptable/paiements',       [App\Http\Controllers\Comptable\PaiementController::class, 'store'])->name('comptable.paiements.store');
    Route::get('/comptable/paiements',        [App\Http\Controllers\Comptable\PaiementController::class, 'index'])->name('comptable.paiements.index');
    Route::get('/comptable/paiements/{paiement}/recu', [App\Http\Controllers\Comptable\PaiementController::class, 'telechargerRecu'])->name('comptable.paiements.recu');


    // Historique des paiements
    Route::get('/comptable/historique', [App\Http\Controllers\Comptable\PaiementController::class, 'index'])->name('comptable.paiements.index');

    // Documents financiers
    Route::get('/comptable/documents', [Comptable\DocumentController::class, 'index'])->name('comptable.documents');
    Route::get('/comptable/documents/rapport-global', [Comptable\DocumentController::class, 'rapportGlobal'])->name('comptable.documents.rapport-global');

    // Profil
    Route::get('/comptable/profil', [Comptable\ProfilController::class, 'index'])->name('comptable.profil');
    Route::put('/comptable/profil/infos', [Comptable\ProfilController::class, 'updateInfos'])->name('comptable.profil.infos');
    Route::put('/comptable/profil/password', [Comptable\ProfilController::class, 'updatePassword'])->name('comptable.profil.password');
});