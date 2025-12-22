<?php

namespace App\Console\Commands;

use App\Helpers\ComponentScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InventoryComponents extends Command
{
    protected $signature = 'inventory:components';

    protected $description = 'Génère l\'inventaire complet des composants avec classification, dépendances JS et data-attributes';

    public function handle(): int
    {
        $this->info('Génération de l\'inventaire des composants...');
        $inventory = ComponentScanner::rebuildCache();
        $components = $inventory['components'] ?? [];
        $dataAttributes = $inventory['dataAttributesMap'] ?? [];
        $jsDeps = $inventory['jsDeps'] ?? [];

        // Générer le CSV des composants
        $csvPath = base_path('docs/inventory');
        File::ensureDirectoryExists($csvPath);
        $csv = fopen($csvPath.'/components.csv', 'w');
        fputcsv($csv, ['name', 'view', 'category', 'tags', 'jsModule', 'status']);
        foreach ($components as $comp) {
            fputcsv($csv, [
                $comp['name'],
                $comp['view'],
                $comp['category'],
                implode(', ', $comp['tags']),
                $comp['jsModule'] ?? '',
                $comp['status'],
            ]);
        }
        fclose($csv);

        // Générer le CSV des data-attributes
        $dataAttrCsv = fopen($csvPath.'/data-attributes.csv', 'w');
        fputcsv($dataAttrCsv, ['attribute', 'components']);
        foreach ($dataAttributes as $attr => $comps) {
            fputcsv($dataAttrCsv, [$attr, implode(', ', $comps)]);
        }
        fclose($dataAttrCsv);

        // Générer le JSON des dépendances JS
        File::put($csvPath.'/js-deps.json', json_encode($jsDeps, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->info('✓ '.count($components).' composants inventoriés');
        $this->info('✓ Cache généré: '.ComponentScanner::cachePath());
        $this->info('✓ CSV généré: docs/inventory/components.csv');
        $this->info('✓ Data-attributes: docs/inventory/data-attributes.csv');
        $this->info('✓ Dépendances JS: docs/inventory/js-deps.json');

        return Command::SUCCESS;
    }
}
