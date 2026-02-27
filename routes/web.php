<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Page de connexion
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

// Traitement du formulaire de connexion
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

// Déconnexion
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Admin et Superadmin

Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
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
});


//  Enseignant

Route::middleware(['auth', 'role:enseignant'])->group(function () {
    Route::get('/enseignant/dashboard', function () {
        return view('enseignant.dashboard');
    })->name('enseignant.dashboard');
});


// Élève

Route::middleware(['auth', 'role:eleve'])->group(function () {
    Route::get('/eleve/dashboard', function () {
        return view('eleve.dashboard');
    })->name('eleve.dashboard');
});


// Comptable

Route::middleware(['auth', 'role:comptable'])->group(function () {
    Route::get('/comptable/dashboard', function () {
        return view('comptable.dashboard');
    })->name('comptable.dashboard');
});