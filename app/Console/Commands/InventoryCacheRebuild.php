<?php

namespace App\Console\Commands;

use App\Helpers\ComponentScanner;
use App\Helpers\TemplateScanner;
use Illuminate\Console\Command;

class InventoryCacheRebuild extends Command
{
    protected $signature = 'inventory:cache:rebuild {--components : Rebuild only components cache} {--templates : Rebuild only templates cache}';

    protected $description = 'Reconstruit le cache fichier des inventaires (composants et/ou templates)';

    public function handle(): int
    {
        $rebuildComponents = $this->option('components') || ! $this->option('templates');
        $rebuildTemplates = $this->option('templates') || ! $this->option('components');

        if ($rebuildComponents) {
            $this->info('Rebuild cache composants...');
            $inventory = ComponentScanner::rebuildCache();
            $this->info('✓ Cache composants OK');
            $this->line('  - '.ComponentScanner::cachePath());
            $this->line('  - '.count($inventory['components'] ?? []).' composants');
        }

        if ($rebuildTemplates) {
            $this->info('Rebuild cache templates...');
            $inventory = TemplateScanner::rebuildCache();
            $this->info('✓ Cache templates OK');
            $this->line('  - '.TemplateScanner::cachePath());
            $this->line('  - '.count($inventory['templates'] ?? []).' templates');
        }

        return Command::SUCCESS;
    }
}
