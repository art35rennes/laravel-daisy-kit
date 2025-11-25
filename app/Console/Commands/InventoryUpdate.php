<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

class InventoryUpdate extends Command
{
    protected $signature = 'inventory:update {--force : Force la régénération de toutes les pages de documentation}';

    protected $description = 'Met à jour l\'inventaire complet (composants, templates et pages de documentation)';

    public function handle(): int
    {
        $this->info('Mise à jour de l\'inventaire...');
        $this->newLine();

        // 0. Nettoyer les caches
        $this->info('0. Nettoyage des caches...');
        Artisan::call('optimize:clear');
        $this->info('✓ Caches nettoyés');
        $this->newLine();

        // 1. Générer l'inventaire des composants
        $this->info('1. Génération de l\'inventaire des composants...');
        $result = Artisan::call('inventory:components');
        if ($result !== Command::SUCCESS) {
            $this->error('Erreur lors de la génération de l\'inventaire des composants.');

            return Command::FAILURE;
        }
        $this->info('✓ Inventaire des composants généré');
        $this->newLine();

        // 2. Générer l'inventaire des templates
        $this->info('2. Génération de l\'inventaire des templates...');
        $result = Artisan::call('inventory:templates');
        if ($result !== Command::SUCCESS) {
            $this->error('Erreur lors de la génération de l\'inventaire des templates.');

            return Command::FAILURE;
        }
        $this->info('✓ Inventaire des templates généré');
        $this->newLine();

        // 3. Générer les pages de documentation
        $this->info('3. Génération des pages de documentation...');
        $options = [];
        if ($this->option('force')) {
            $options['--force'] = true;
        }
        $result = Artisan::call('docs:generate-pages', $options);
        if ($result !== Command::SUCCESS) {
            $this->error('Erreur lors de la génération des pages de documentation.');

            return Command::FAILURE;
        }
        $this->info('✓ Pages de documentation générées');
        $this->newLine();

        // 4. Compiler les assets
        $this->info('4. Compilation des assets...');
        $process = Process::run('npm run build');
        if (! $process->successful()) {
            $this->error('Erreur lors de la compilation des assets.');
            $this->error($process->errorOutput());

            return Command::FAILURE;
        }
        $this->info('✓ Assets compilés');
        $this->newLine();

        $this->info('✓ Mise à jour de l\'inventaire terminée avec succès !');

        return Command::SUCCESS;
    }
}
