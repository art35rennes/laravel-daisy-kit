<?php

namespace App\Console\Commands;

use App\Helpers\TemplateScanner;
use Illuminate\Console\Command;

class InventoryTemplates extends Command
{
    protected $signature = 'inventory:templates';

    protected $description = 'Génère l\'inventaire complet des templates avec catégories et routes';

    public function handle(): int
    {
        $this->info('Génération de l\'inventaire des templates...');
        $inventory = TemplateScanner::rebuildCache();
        $templates = $inventory['templates'] ?? [];

        $this->info('✓ '.count($templates).' templates inventoriés');
        $this->info('✓ Cache généré: '.TemplateScanner::cachePath());

        return Command::SUCCESS;
    }
}
