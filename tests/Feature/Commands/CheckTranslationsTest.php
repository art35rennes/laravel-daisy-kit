<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(langPath('check'));
    File::ensureDirectoryExists(langPath('check'));
});

afterEach(function () {
    File::deleteDirectory(langPath('check'));
});

it('succeeds when all locales share the same keys', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
        'nested' => [
            'first' => 'A value',
        ],
    ], 'check');

    writeTranslations('fr', 'messages', [
        'title' => 'Titre',
        'nested' => [
            'first' => 'Une valeur',
        ],
    ], 'check');

    $exitCode = Artisan::call('translations:check', [
        '--path' => langPath('check'),
        '--locale' => 'en',
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($output)->toContain('Locales disponibles (incluant référence): en, fr')
        ->and($output)->toContain('Fichiers de référence: 1')
        ->and($output)->toContain('Total clés de référence: 2')
        ->and($output)->toContain('Clés manquantes totales: 0')
        ->and($output)->toContain('Toutes les traductions sont complètes');
});

it('fails when a locale misses a translation key', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
        'nested' => [
            'first' => 'A value',
            'second' => 'Another value',
        ],
    ], 'check');

    writeTranslations('fr', 'messages', [
        'title' => 'Titre',
        'nested' => [
            'first' => 'Une valeur',
        ],
    ], 'check');

    $exitCode = Artisan::call('translations:check', [
        '--path' => langPath('check'),
        '--locale' => 'en',
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($output)->toContain('[fr] messages.php')
        ->and($output)->toContain('nested.second')
        ->and($output)->toContain('Détail des clés manquantes (locale / fichier / clé):')
        ->and($output)->toContain('Clés manquantes totales: 1');
});

it('fails when a translation file is missing', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
    ], 'check');

    File::ensureDirectoryExists(langPath('check').'/fr');

    $exitCode = Artisan::call('translations:check', [
        '--path' => langPath('check'),
        '--locale' => 'en',
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($output)->toContain('Locales disponibles (incluant référence): en, fr')
        ->and($output)->toContain('Fichier manquant:')
        ->and($output)->toContain('Détail des clés manquantes (locale / fichier / clé):')
        ->and($output)->toContain('Clés manquantes totales: 1');
});


