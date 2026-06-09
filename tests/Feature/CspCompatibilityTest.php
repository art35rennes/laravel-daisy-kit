<?php

use Illuminate\Support\Facades\File;

it('does not ship executable inline scripts or event handler attributes in Blade views', function () {
    $views = collect(File::allFiles(dirname(__DIR__, 2).'/resources/views'))
        ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php');

    $violations = [];

    foreach ($views as $view) {
        $contents = $view->getContents();
        $relativePath = $view->getRelativePathname();

        if (preg_match_all('/<script\b([^>]*)>/i', $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes = $match[1] ?? '';
                $isDataScript = preg_match('/\btype\s*=\s*["\']application\/(?:json|ld\+json)["\']/i', $attributes) === 1;

                if (! $isDataScript) {
                    $violations[] = "{$relativePath} contains executable inline script";
                }
            }
        }

        if (preg_match('/\son[a-zA-Z]+\s*=/i', $contents)) {
            $violations[] = "{$relativePath} contains inline event handler attribute";
        }
    }

    expect($violations)->toBe([]);
});
