<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\AnneeAcademique;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $anneeActive = AnneeAcademique::active()->first();
            $view->with('anneeActiveLayout', $anneeActive);
        });

        View::composer('layouts.enseignant', function ($view) {
            $anneeActive = AnneeAcademique::active()->first();
            $view->with('anneeActiveLayout', $anneeActive);
        });

        View::composer('layouts.eleve', function ($view) {
            $anneeActive = AnneeAcademique::active()->first();
            $view->with('anneeActiveLayout', $anneeActive);
        });

        View::composer('layouts.comptable', function ($view) {
            $anneeActive = AnneeAcademique::active()->first();
            $view->with('anneeActiveLayout', $anneeActive);
        });

        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }  
    }
}