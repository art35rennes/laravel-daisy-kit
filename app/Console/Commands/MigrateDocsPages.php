<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateDocsPages extends Command
{
    protected $signature = 'docs:migrate-pages {--category= : Migrer seulement une catégorie spécifique} {--dry-run : Afficher ce qui serait fait sans modifier les fichiers}';

    protected $description = 'Migre les pages de documentation de l\'ancien système vers le nouveau';

    public function handle(): int
    {
        $docsPath = resource_path('dev/views/docs/components');
        $dryRun = $this->option('dry-run');
        $categoryFilter = $this->option('category');

        if (! File::isDirectory($docsPath)) {
            $this->error("Le dossier n'existe pas: {$docsPath}");

            return Command::FAILURE;
        }

        $categories = $categoryFilter ? [$categoryFilter] : File::directories($docsPath);
        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($categories as $categoryPath) {
            $category = is_string($categoryPath) ? $categoryPath : basename($categoryPath);
            
            if (is_string($categoryPath)) {
                $categoryPath = $docsPath.'/'.$category;
            }

            if (! File::isDirectory($categoryPath)) {
                continue;
            }

            $files = File::glob($categoryPath.'/*.blade.php');

            foreach ($files as $file) {
                $name = basename($file, '.blade.php');
                
                // Ignorer index.blade.php
                if ($name === 'index') {
                    continue;
                }

                // Vérifier si déjà migré
                $content = File::get($file);
                if (str_contains($content, 'x-daisy::docs.page')) {
                    $skipped++;
                    continue;
                }

                // Vérifier si c'est l'ancien format
                if (! str_contains($content, 'x-daisy::layout.docs')) {
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->info("Migrerait: {$category}/{$name}");
                    $migrated++;
                } else {
                    try {
                        $this->migratePage($file, $category, $name);
                        $migrated++;
                        $this->info("✓ Migré: {$category}/{$name}");
                    } catch (\Exception $e) {
                        $errors++;
                        $this->error("✗ Erreur sur {$category}/{$name}: {$e->getMessage()}");
                    }
                }
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("Mode dry-run: {$migrated} fichiers seraient migrés, {$skipped} ignorés");
        } else {
            $this->info("✓ {$migrated} fichiers migrés");
            if ($skipped > 0) {
                $this->info("  {$skipped} fichiers ignorés (déjà migrés ou format inconnu)");
            }
            if ($errors > 0) {
                $this->error("  {$errors} erreurs");
            }
        }

        return Command::SUCCESS;
    }

    private function migratePage(string $filePath, string $category, string $name): void
    {
        $content = File::get($filePath);

        // Extraire les sections existantes
        $sections = $this->extractSections($content);
        
        // Extraire le titre
        $title = $this->extractTitle($content);
        
        // Extraire la description/subtitle
        $subtitle = $this->extractSubtitle($content);

        // Générer le nouveau contenu
        $newContent = $this->generateNewContent($category, $name, $title, $subtitle, $sections, $content);

        File::put($filePath, $newContent);
    }

    private function extractSections(string $content): array
    {
        $sections = [['id' => 'intro', 'label' => 'Introduction']];
        
        if (str_contains($content, 'id="base"')) {
            $sections[] = ['id' => 'base', 'label' => 'Exemple de base'];
        }
        if (str_contains($content, 'id="variants"')) {
            $sections[] = ['id' => 'variants', 'label' => 'Variantes'];
        }
        if (str_contains($content, 'id="types"')) {
            $sections[] = ['id' => 'types', 'label' => 'Types'];
        }
        if (str_contains($content, 'id="slots"')) {
            $sections[] = ['id' => 'slots', 'label' => 'Slots'];
        }
        if (str_contains($content, 'id="api"')) {
            $sections[] = ['id' => 'api', 'label' => 'API'];
        }

        return $sections;
    }

    private function extractTitle(string $content): string
    {
        if (preg_match('/<x-daisy::layout\.docs\s+title="([^"]+)"/', $content, $match)) {
            return $match[1];
        }
        if (preg_match('/<h1>([^<]+)<\/h1>/', $content, $match)) {
            return trim($match[1]);
        }

        return 'Composant';
    }

    private function extractSubtitle(string $content): string
    {
        if (preg_match('/<h2>([^<]+)<\/h2>/', $content, $match)) {
            return trim($match[1]);
        }
        if (preg_match('/<p>([^<]+)<\/p>/', $content, $match)) {
            return trim($match[1]);
        }

        return 'Composant compatible daisyUI v5 et Tailwind CSS v4.';
    }

    private function generateNewContent(string $category, string $name, string $title, string $subtitle, array $sections, string $oldContent): string
    {
        $sectionsPhp = "[\n";
        foreach ($sections as $section) {
            $sectionsPhp .= "        ['id' => '{$section['id']}', 'label' => '{$section['label']}'],\n";
        }
        $sectionsPhp .= '    ]';

        // Extraire les sections de contenu (base, variants, etc.) depuis l'ancien contenu
        // Pour l'instant, on génère une structure minimale
        $baseSection = $this->extractSection($oldContent, 'base', $name);
        $variantsSection = $this->extractSection($oldContent, 'variants', $name);
        $customSections = $this->extractCustomSections($oldContent, $sections);

        return <<<BLADE
@php
    use App\Helpers\DocsHelper;
    \$prefix = config('daisy-kit.docs.prefix', 'docs');
    \$category = '{$category}';
    \$name = '{$name}';
    \$sections = {$sectionsPhp};
    \$props = DocsHelper::getComponentProps(\$category, \$name);
@endphp

<x-daisy::docs.page 
    title="{$title}" 
    category="{$category}" 
    name="{$name}"
    type="component"
    :sections="\$sections"
>
    <x-slot:intro>
        <x-daisy::docs.sections.intro 
            title="{$title}" 
            subtitle="{$subtitle}"
        />
    </x-slot:intro>

{$baseSection}

{$variantsSection}

{$customSections}

    <x-daisy::docs.sections.api :category="\$category" :name="\$name" />
</x-daisy::docs.page>
BLADE;
    }

    private function extractSection(string $content, string $sectionId, string $componentName): string
    {
        // Extraire la section base ou variants et la convertir
        if (! str_contains($content, "id=\"{$sectionId}\"")) {
            return '';
        }

        // Pour l'instant, retourner un placeholder
        // L'utilisateur devra compléter manuellement
        if ($sectionId === 'base') {
            return <<<'BLADE'
    <x-daisy::docs.sections.example name="'.$componentName.'">
        <x-slot:preview>
            <!-- À compléter -->
        </x-slot:preview>
        <x-slot:code>
            <!-- À compléter -->
        </x-slot:code>
    </x-daisy::docs.sections.example>
BLADE;
        }

        if ($sectionId === 'variants') {
            return <<<'BLADE'
    <x-daisy::docs.sections.variants name="'.$componentName.'">
        <x-slot:preview>
            <!-- À compléter -->
        </x-slot:preview>
        <x-slot:code>
            <!-- À compléter -->
        </x-slot:code>
    </x-daisy::docs.sections.variants>
BLADE;
        }

        return '';
    }

    private function extractCustomSections(string $content, array $sections): string
    {
        $custom = '';
        foreach ($sections as $section) {
            if (in_array($section['id'], ['intro', 'base', 'variants', 'api'])) {
                continue;
            }

            $custom .= <<<BLADE
    <x-daisy::docs.sections.custom id="{$section['id']}" title="{$section['label']}">
        <!-- À compléter -->
    </x-daisy::docs.sections.custom>

BLADE;
        }

        return $custom;
    }
}

