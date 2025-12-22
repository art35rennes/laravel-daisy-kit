<?php

namespace App\Console\Commands;

use App\Helpers\ComponentScanner;
use App\Helpers\TemplateScanner;
use Illuminate\Console\Command;

class InventoryCacheClear extends Command
{
    protected $signature = 'inventory:cache:clear {--components : Clear only components cache} {--templates : Clear only templates cache}';

    protected $description = 'Supprime les caches fichiers des inventaires (composants et/ou templates)';

    public function handle(): int
    {
        $clearComponents = $this->option('components') || ! $this->option('templates');
        $clearTemplates = $this->option('templates') || ! $this->option('components');

        if ($clearComponents) {
            ComponentScanner::clearCache();
            $this->info('✓ Cache composants supprimé');
        }

        if ($clearTemplates) {
            TemplateScanner::clearCache();
            $this->info('✓ Cache templates supprimé');
        }

        return Command::SUCCESS;
    }
}
