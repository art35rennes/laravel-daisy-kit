<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo', function () {
    return view('demo');
})->name('demo');

// Pages dédiées aux layouts/templates avancés
Route::view('/templates', 'templates.index')->name('templates.index');
Route::view('/templates/navbar', 'templates.navbar')->name('layouts.navbar');
Route::view('/templates/sidebar', 'templates.sidebar')->name('layouts.sidebar');
Route::view('/templates/navbar-sidebar', 'templates.navbar-sidebar')->name('layouts.navbar-sidebar');
