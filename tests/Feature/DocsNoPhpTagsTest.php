<?php

use Illuminate\Support\Facades\File;

it('does not contain raw PHP open/close tags in documentation views (prevents parser crashes)', function () {
    $pathsToScan = [
        resource_path('dev/views/docs'),
        resource_path('views/components/docs'),
    ];

    $violations = [];

    foreach ($pathsToScan as $root) {
        if (! File::isDirectory($root)) {
            continue;
        }

        foreach (File::allFiles($root) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $content = File::get($file->getPathname());

            if (! str_contains($content, '<?php') && ! str_contains($content, '?>')) {
                continue;
            }

            $lines = preg_split("/\r\n|\n|\r/", $content) ?: [];
            foreach ($lines as $index => $line) {
                if (! str_contains($line, '<?php') && ! str_contains($line, '?>')) {
                    continue;
                }

                $violations[] = $file->getRelativePathname().':'.($index + 1).' '.$line;
            }
        }
    }

    expect($violations)
        ->toBeEmpty("Raw PHP tags found in documentation views:\n".implode("\n", $violations));
});
