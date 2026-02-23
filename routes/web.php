<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/enseignant/dashboard', function () {
    return view('enseignant.dashboard');
});

Route::get('/eleve/dashboard', function () {
    return view('eleve.dashboard');
});

Route::get('/comptable/dashboard', function () {
    return view('comptable.dashboard');
});