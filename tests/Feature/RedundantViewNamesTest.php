<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

it('détecte les noms de vues redondants dans les templates', function () {
    $templatesPath = resource_path('views/templates');
    
    if (! File::isDirectory($templatesPath)) {
        return;
    }

    $files = File::allFiles($templatesPath);
    $redundantNames = [];

    foreach ($files as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        $relativePath = str_replace([$templatesPath, '\\'], ['', '/'], $file->getPathname());
        $relativePath = ltrim($relativePath, '/');
        
        if (str_contains($relativePath, '/partials/')) {
            continue;
        }

        $relativeWithoutExtension = str_replace('.blade.php', '', $relativePath);
        $segments = explode('/', $relativeWithoutExtension);
        
        // Détecter si le dernier segment répète le segment précédent
        if (count($segments) >= 2) {
            $lastSegment = $segments[count($segments) - 1];
            $previousSegment = $segments[count($segments) - 2];
            
            if ($lastSegment === $previousSegment) {
                $viewPath = 'daisy::templates.'.str_replace('/', '.', $relativeWithoutExtension);
                $redundantNames[] = [
                    'file' => $relativePath,
                    'view' => $viewPath,
                    'expected' => 'daisy::templates.'.str_replace('/', '.', implode('.', array_slice($segments, 0, -1))),
                ];
            }
        }
    }

    expect($redundantNames)->toBeEmpty(
        'Des noms de vues redondants ont été détectés: '.json_encode($redundantNames, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
})->group('naming');

it('détecte les noms de composants redondants dans les composants UI', function () {
    $componentsPath = resource_path('views/components/ui');
    
    if (! File::isDirectory($componentsPath)) {
        return;
    }

    $files = File::allFiles($componentsPath);
    $redundantNames = [];

    foreach ($files as $file) {
        if (! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        $relativePath = str_replace([$componentsPath, '\\'], ['', '/'], $file->getPathname());
        $relativePath = ltrim($relativePath, '/');

        $relativeWithoutExtension = str_replace('.blade.php', '', $relativePath);
        $segments = explode('/', $relativeWithoutExtension);
        
        // Détecter si le dernier segment répète le segment précédent
        if (count($segments) >= 2) {
            $lastSegment = $segments[count($segments) - 1];
            $previousSegment = $segments[count($segments) - 2];
            
            if ($lastSegment === $previousSegment) {
                $viewPath = 'daisy::ui.'.str_replace('/', '.', $relativeWithoutExtension);
                $redundantNames[] = [
                    'file' => $relativePath,
                    'view' => $viewPath,
                    'expected' => 'daisy::ui.'.str_replace('/', '.', implode('.', array_slice($segments, 0, -1))),
                ];
            }
        }
    }

    expect($redundantNames)->toBeEmpty(
        'Des noms de composants redondants ont été détectés: '.json_encode($redundantNames, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
})->group('naming');

