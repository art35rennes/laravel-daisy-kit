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

        // 1. Générer/reconstruire les caches d'inventaire
        $this->info('1. Reconstruction du cache des inventaires...');
        $result = Artisan::call('inventory:cache:rebuild');
        if ($result !== Command::SUCCESS) {
            $this->error('Erreur lors de la reconstruction du cache des inventaires.');

            return Command::FAILURE;
        }
        $this->info('✓ Cache des inventaires reconstruit');
        $this->newLine();

        // 2. Régénérer les artefacts dev (CSV, etc.) via la commande composants
        $this->info('2. Génération des artefacts dev (CSV, js-deps)...');
        $result = Artisan::call('inventory:components');
        if ($result !== Command::SUCCESS) {
            $this->error('Erreur lors de la génération des artefacts dev.');

            return Command::FAILURE;
        }
        $this->info('✓ Artefacts dev générés');
        $this->newLine();

        $this->info('✓ Mise à jour de l\'inventaire terminée avec succès !');

        return Command::SUCCESS;
    }
}
