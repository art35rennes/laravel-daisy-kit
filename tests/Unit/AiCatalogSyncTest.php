<?php

use Illuminate\Support\Facades\File;

it('keeps the generated ai catalog in sync with the public blade surface', function () {
    $catalogPath = packagePath('resources/boost/skills/daisy-kit-component-reuse/references/components.json');

    expect(File::exists($catalogPath))->toBeTrue();

    $catalog = json_decode((string) file_get_contents($catalogPath), true, 512, JSON_THROW_ON_ERROR);

    $componentAliases = collect(File::allFiles(packagePath('resources/views/components')))
        ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php'))
        ->map(function ($file) {
            $relative = str_replace(
                [packagePath('resources/views/components').DIRECTORY_SEPARATOR, '\\'],
                ['', '/'],
                $file->getPathname()
            );

            if (str_starts_with($relative, 'templates/')) {
                return null;
            }

            return 'x-daisy::'.str_replace('/', '.', str_replace('.blade.php', '', $relative));
        })
        ->filter()
        ->sort()
        ->values()
        ->all();

    $templateAliases = collect(File::allFiles(packagePath('resources/views/templates')))
        ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php'))
        ->map(function ($file) {
            $relative = str_replace(
                [packagePath('resources/views/templates').DIRECTORY_SEPARATOR, '\\'],
                ['', '/'],
                $file->getPathname()
            );

            $logicalName = str_replace('.blade.php', '', $relative);

            return [
                'component_alias' => 'x-daisy::templates.'.str_replace('/', '.', $logicalName),
                'view_alias' => 'daisy::templates.'.str_replace('/', '.', $logicalName),
            ];
        })
        ->sortBy('component_alias')
        ->values()
        ->all();

    expect($catalog['summary']['component_count'])->toBe(count($componentAliases))
        ->and($catalog['summary']['template_count'])->toBe(count($templateAliases))
        ->and(collect($catalog['components'])->pluck('alias')->all())->toEqual($componentAliases)
        ->and(collect($catalog['templates'])->map(fn (array $entry) => [
            'component_alias' => $entry['component_alias'],
            'view_alias' => $entry['view_alias'],
        ])->all())->toEqual($templateAliases)
        ->and(collect($catalog['components'])->pluck('alias'))->toContain('x-daisy::layout.app')
        ->and(collect($catalog['components'])->pluck('alias'))->toContain('x-daisy::ui.inputs.button')
        ->and(collect($catalog['components'])->pluck('alias'))->toContain('x-daisy::ui.data-display.table')
        ->and(collect($catalog['templates'])->pluck('component_alias'))->toContain('x-daisy::templates.auth.login-simple');
});

it('ships the boost reuse skill and human-readable catalog references', function () {
    $skillPath = packagePath('resources/boost/skills/daisy-kit-component-reuse/SKILL.md');
    $catalogPath = packagePath('resources/boost/skills/daisy-kit-component-reuse/references/component-catalog.md');

    expect(File::exists($skillPath))->toBeTrue()
        ->and(File::exists($catalogPath))->toBeTrue()
        ->and((string) file_get_contents($skillPath))->toContain('name: daisy-kit-component-reuse')
        ->and((string) file_get_contents($catalogPath))->toContain('`x-daisy::ui.inputs.button`')
        ->and((string) file_get_contents($catalogPath))->toContain('`x-daisy::templates.auth.login-simple`');
});
