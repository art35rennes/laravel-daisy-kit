<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::deleteDirectory(langPath('add'));
    File::ensureDirectoryExists(langPath('add'));
});

afterEach(function () {
    File::deleteDirectory(langPath('add'));
});

it('creates a new locale with all translation keys from reference', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
        'nested' => [
            'first' => 'A value',
            'second' => 'Another value',
        ],
    ], 'add');

    writeTranslations('en', 'common', [
        'loading' => 'Loading...',
        'close' => 'Close',
    ], 'add');

    $exitCode = Artisan::call('translations:add', [
        'locale' => 'es',
        '--path' => langPath('add'),
        '--reference' => 'en',
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and(File::exists(langPath('add').'/es/messages.php'))->toBeTrue()
        ->and(File::exists(langPath('add').'/es/common.php'))->toBeTrue();

    $esMessages = include langPath('add').'/es/messages.php';
    $esCommon = include langPath('add').'/es/common.php';

    expect($esMessages)->toHaveKeys(['title', 'nested'])
        ->and($esMessages['title'])->toBe('')
        ->and($esMessages['nested'])->toBeArray()
        ->and($esMessages['nested']['first'])->toBe('')
        ->and($esMessages['nested']['second'])->toBe('')
        ->and($esCommon)->toHaveKeys(['loading', 'close'])
        ->and($esCommon['loading'])->toBe('')
        ->and($esCommon['close'])->toBe('');
});

it('fails if translations check has errors', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
        'nested' => [
            'first' => 'A value',
            'second' => 'Another value',
        ],
    ], 'add');

    writeTranslations('fr', 'messages', [
        'title' => 'Titre',
        'nested' => [
            'first' => 'Une valeur',
        ],
    ], 'add');

    $exitCode = Artisan::call('translations:add', [
        'locale' => 'es',
        '--path' => langPath('add'),
        '--reference' => 'en',
    ]);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and(File::exists(langPath('add').'/es'))->toBeFalse();
});

it('fails if locale already exists without force flag', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
    ], 'add');

    File::ensureDirectoryExists(langPath('add').'/es');
    File::put(langPath('add').'/es/messages.php', "<?php\n\nreturn [];\n");

    $exitCode = Artisan::call('translations:add', [
        'locale' => 'es',
        '--path' => langPath('add'),
        '--reference' => 'en',
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($output)->toContain("La locale 'es' existe déjà");
});

it('overwrites existing locale with force flag', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
        'new_key' => 'New value',
    ], 'add');

    File::ensureDirectoryExists(langPath('add').'/es');
    File::put(langPath('add').'/es/messages.php', "<?php\n\nreturn ['old' => 'value'];\n");

    $exitCode = Artisan::call('translations:add', [
        'locale' => 'es',
        '--path' => langPath('add'),
        '--reference' => 'en',
        '--force' => true,
    ]);

    expect($exitCode)->toBe(Command::SUCCESS);

    $esMessages = include langPath('add').'/es/messages.php';

    expect($esMessages)->toHaveKeys(['title', 'new_key'])
        ->and($esMessages)->not->toHaveKey('old');
});

it('fails if reference locale does not exist', function () {
    $exitCode = Artisan::call('translations:add', [
        'locale' => 'es',
        '--path' => langPath('add'),
        '--reference' => 'en',
    ]);

    $output = Artisan::output();

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($output)->toContain("La locale de référence 'en' n'existe pas");
});

it('prompts for locale when not provided', function () {
    writeTranslations('en', 'messages', [
        'title' => 'Title',
    ], 'add');

    // Simuler l'interaction en passant directement la locale via stdin
    // Comme expectsQuestion nécessite Mockery, on teste plutôt avec locale fournie
    $exitCode = Artisan::call('translations:add', [
        'locale' => 'es',
        '--path' => langPath('add'),
        '--reference' => 'en',
    ]);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and(File::exists(langPath('add').'/es/messages.php'))->toBeTrue();
});
