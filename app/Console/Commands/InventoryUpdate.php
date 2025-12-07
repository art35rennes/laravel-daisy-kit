<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InventoryUpdate extends Command
{
    protected $signature = 'inventory:update';

    protected $description = 'Met à jour l\'inventaire complet (composants et templates)';

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

        $this->info('✓ Mise à jour de l\'inventaire terminée avec succès !');

        return Command::SUCCESS;
    }
}
