<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'workbench::index')->name('workbench.index');
