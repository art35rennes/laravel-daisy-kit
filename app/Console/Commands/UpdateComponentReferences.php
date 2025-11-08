<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateComponentReferences extends Command
{
    protected $signature = 'update:component-references {--dry-run : Affiche ce qui sera modifié sans modifier les fichiers}';

    protected $description = 'Met à jour toutes les références aux composants avec les nouveaux chemins par catégories';

    private array $categoryMap = [];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        // Construire le mapping nom → catégorie
        $categories = [
            'inputs' => ['button', 'input', 'textarea', 'select', 'checkbox', 'radio', 'range', 'toggle', 'file-input', 'color-picker'],
            'navigation' => ['breadcrumbs', 'menu', 'pagination', 'navbar', 'sidebar', 'tabs', 'steps', 'stepper'],
            'layout' => ['card', 'hero', 'footer', 'divider', 'list', 'list-row', 'stack'],
            'data-display' => ['badge', 'avatar', 'kbd', 'table', 'stat', 'progress', 'radial-progress', 'status', 'timeline'],
            'overlay' => ['modal', 'drawer', 'dropdown', 'popover', 'popconfirm', 'tooltip'],
            'media' => ['carousel', 'lightbox', 'media-gallery', 'embed', 'leaflet'],
            'feedback' => ['alert', 'toast', 'loading', 'skeleton', 'callout'],
            'utilities' => ['mockup-browser', 'mockup-code', 'mockup-phone', 'mockup-window', 'indicator', 'dock'],
            'advanced' => ['accordion', 'calendar', 'calendar-full', 'calendar-cally', 'calendar-native', 'chart', 'chat-bubble', 'code-editor', 'collapse', 'countdown', 'diff', 'fieldset', 'filter', 'icon', 'join', 'label', 'link', 'login-button', 'mask', 'onboarding', 'rating', 'scroll-status', 'scrollspy', 'swap', 'theme-controller', 'transfer', 'tree-view', 'validator', 'wysiwyg'],
        ];

        foreach ($categories as $category => $components) {
            foreach ($components as $component) {
                $this->categoryMap[$component] = $category;
            }
        }

        $this->info('Mise à jour des références aux composants...');

        // Dossiers à scanner
        $directories = [
            resource_path('dev/views'),
            resource_path('views/components'),
        ];

        $stats = ['files' => 0, 'replacements' => 0];

        foreach ($directories as $dir) {
            if (! File::isDirectory($dir)) {
                continue;
            }

            $files = File::allFiles($dir);
            foreach ($files as $file) {
                if ($file->getExtension() !== 'php' && $file->getExtension() !== 'blade.php') {
                    continue;
                }

                $content = File::get($file->getPathname());
                $originalContent = $content;
                $fileReplacements = 0;

                // Remplacer les références daisy::components.ui.{name}
                foreach ($this->categoryMap as $component => $category) {
                    $oldPattern = "daisy::components.ui.{$component}";
                    $newPattern = "daisy::components.ui.{$category}.{$component}";

                    // Pattern avec guillemets simples
                    $content = str_replace("'{$oldPattern}'", "'{$newPattern}'", $content, $count1);
                    // Pattern avec guillemets doubles
                    $content = str_replace('"'.$oldPattern.'"', '"'.$newPattern.'"', $content, $count2);
                    // Pattern dans @include
                    $content = str_replace("@include('{$oldPattern}'", "@include('{$newPattern}'", $content, $count3);
                    $content = str_replace('@include("'.$oldPattern.'"', '@include("'.$newPattern.'"', $content, $count4);
                    // Pattern dans x-daisy::
                    $content = str_replace("x-daisy::ui.{$component}", "x-daisy::ui.{$category}.{$component}", $content, $count5);

                    $fileReplacements += $count1 + $count2 + $count3 + $count4 + $count5;
                }

                if ($content !== $originalContent) {
                    $stats['files']++;
                    $stats['replacements'] += $fileReplacements;

                    if ($dryRun) {
                        $this->line("  [DRY-RUN] {$file->getRelativePathname()} ({$fileReplacements} remplacements)");
                    } else {
                        File::put($file->getPathname(), $content);
                        $this->info("  ✓ {$file->getRelativePathname()} ({$fileReplacements} remplacements)");
                    }
                }
            }
        }

        $this->newLine();
        $this->info("Résumé:");
        $this->line("  - Fichiers modifiés: {$stats['files']}");
        $this->line("  - Remplacements: {$stats['replacements']}");

        return Command::SUCCESS;
    }
}
