<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Page de démo réservée au dev (non publiée) via le namespace daisy-dev
Route::get('/demo', function () {
    return view('daisy-dev::demo.index');
})->name('demo');

// Pages dédiées aux layouts/templates avancés
Route::view('/templates', 'templates.index')->name('templates.index');
Route::view('/templates/navbar', 'templates.navbar')->name('layouts.navbar');
Route::view('/templates/sidebar', 'templates.sidebar')->name('layouts.sidebar');
Route::view('/templates/navbar-sidebar', 'templates.navbar-sidebar')->name('layouts.navbar-sidebar');
