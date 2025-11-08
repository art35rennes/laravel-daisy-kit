<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateComponentsToCategories extends Command
{
    protected $signature = 'migrate:components-to-categories {--dry-run : Affiche ce qui sera fait sans modifier les fichiers}';

    protected $description = 'Migre les composants vers la structure par catégories';

    private array $moved = [];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('Migration des composants vers les catégories...');

        // Lire le manifeste
        $manifestPath = resource_path('dev/data/components.json');
        if (! File::exists($manifestPath)) {
            $this->error('Manifeste non trouvé. Exécutez d\'abord: php artisan inventory:components');

            return Command::FAILURE;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        $components = $manifest['components'] ?? [];

        $sourceDir = resource_path('views/components/ui');
        $stats = ['moved' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($components as $component) {
            $name = $component['name'];
            $category = $component['category'];
            $sourceFile = "{$sourceDir}/{$name}.blade.php";

            if (! File::exists($sourceFile)) {
                $this->warn("  ⚠ Composant non trouvé: {$name}.blade.php");
                $stats['skipped']++;
                continue;
            }

            $targetDir = "{$sourceDir}/{$category}";
            $targetFile = "{$targetDir}/{$name}.blade.php";

            if (File::exists($targetFile)) {
                $this->warn("  ⚠ Déjà déplacé: {$name} → {$category}/");
                $stats['skipped']++;
                continue;
            }

            if ($dryRun) {
                $this->line("  [DRY-RUN] {$name} → {$category}/");
                $stats['moved']++;
            } else {
                try {
                    File::ensureDirectoryExists($targetDir);
                    File::move($sourceFile, $targetFile);
                    $this->info("  ✓ {$name} → {$category}/");
                    $this->moved[] = ['name' => $name, 'category' => $category, 'old' => "ui.{$name}", 'new' => "ui.{$category}.{$name}"];
                    $stats['moved']++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Erreur pour {$name}: {$e->getMessage()}");
                    $stats['errors']++;
                }
            }
        }

        $this->newLine();
        $this->info("Résumé:");
        $this->line("  - Déplacés: {$stats['moved']}");
        $this->line("  - Ignorés: {$stats['skipped']}");
        $this->line("  - Erreurs: {$stats['errors']}");

        if (! $dryRun && $stats['moved'] > 0) {
            $this->newLine();
            $this->warn('⚠️  N\'oubliez pas de mettre à jour les références dans:');
            $this->line('  - resources/dev/views/demo/*');
            $this->line('  - resources/views/components/layout/*');
            $this->line('  - resources/views/components/ui/partials/*');
            $this->line('  - Tous les fichiers qui utilisent daisy::components.ui.*');
        }

        return Command::SUCCESS;
    }
}
