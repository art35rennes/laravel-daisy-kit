<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Throwable;

class AddTranslation extends Command
{
    protected $signature = 'translations:add
        {locale? : La locale à ajouter (ex: es, de, it)}
        {--path= : Chemin personnalisé des traductions (par défaut resources/lang)}
        {--reference= : Locale de référence (par défaut app.fallback_locale)}
        {--force : Forcer l\'ajout même si la locale existe déjà}';

    protected $description = 'Ajoute une nouvelle langue au package en pré-créant toutes les clés de traduction.';

    public function handle(): int
    {
        $langPath = $this->resolveLangPath($this->option('path'));
        $referenceLocale = $this->option('reference') ?: config('app.fallback_locale', 'en');

        if (! File::isDirectory($langPath)) {
            $this->error("Dossier de traductions introuvable: {$langPath}");

            return Command::FAILURE;
        }

        $locale = $this->argument('locale');

        if ($locale === null) {
            $locale = $this->askForLocale($langPath, $referenceLocale);

            if ($locale === null) {
                return Command::FAILURE;
            }
        }

        $targetPath = $langPath.'/'.$locale;

        if (File::isDirectory($targetPath) && ! $this->option('force')) {
            $this->error("La locale '{$locale}' existe déjà. Utilisez --force pour l'écraser.");

            return Command::FAILURE;
        }

        $referencePath = $langPath.'/'.$referenceLocale;

        if (! File::isDirectory($referencePath)) {
            $this->error("La locale de référence '{$referenceLocale}' n'existe pas dans {$langPath}.");

            return Command::FAILURE;
        }

        // Si --force est utilisé, supprimer la locale cible avant la vérification
        // pour éviter que la vérification échoue à cause d'une locale incomplète
        if ($this->option('force') && File::isDirectory($targetPath)) {
            File::deleteDirectory($targetPath);
        }

        $this->info('Vérification des traductions existantes...');

        $checkExitCode = Artisan::call('translations:check', [
            '--path' => $langPath,
            '--locale' => $referenceLocale,
        ]);

        if ($checkExitCode !== Command::SUCCESS) {
            $this->newLine();
            $this->error('La vérification des traductions a échoué. Veuillez corriger les erreurs avant d\'ajouter une nouvelle langue.');
            $this->line(Artisan::output());

            return Command::FAILURE;
        }

        $this->info('✓ Toutes les traductions existantes sont complètes.');

        $referenceFiles = $this->getTranslationFiles($referencePath);

        if ($referenceFiles->isEmpty()) {
            $this->error("Aucun fichier de traduction trouvé pour la locale de référence {$referenceLocale}.");

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("Création de la locale '{$locale}' basée sur '{$referenceLocale}'...");

        File::ensureDirectoryExists($targetPath);

        $createdCount = 0;
        $totalKeys = 0;

        foreach ($referenceFiles as $fileName => $translations) {
            if ($translations === null) {
                $this->warn("Fichier de référence invalide ignoré: {$fileName}");

                continue;
            }

            $targetFile = $targetPath.'/'.$fileName;
            $newTranslations = $this->createEmptyTranslations($translations);

            if ($this->writeTranslationFile($targetFile, $newTranslations)) {
                $keyCount = count(Arr::dot($newTranslations));
                $totalKeys += $keyCount;
                $createdCount++;
                $this->line("  ✓ {$fileName} ({$keyCount} clés)");
            } else {
                $this->error("  ✗ Erreur lors de la création de {$fileName}");
            }
        }

        $this->newLine();
        $this->info("✓ Locale '{$locale}' créée avec succès !");
        $this->line("  - Fichiers créés: {$createdCount}");
        $this->line("  - Total clés: {$totalKeys}");

        $this->newLine();
        $this->comment("Vous pouvez maintenant remplir les traductions dans: {$targetPath}");

        return Command::SUCCESS;
    }

    private function resolveLangPath(?string $pathOption): string
    {
        if ($pathOption === null || $pathOption === '') {
            return resource_path('lang');
        }

        $normalized = str_replace('\\', '/', $pathOption);
        $resolved = realpath($normalized) ?: realpath(base_path($normalized));

        return $resolved ?: $normalized;
    }

    /**
     * @return \Illuminate\Support\Collection<string, array<string, mixed>|null>
     */
    private function getTranslationFiles(string $localePath): \Illuminate\Support\Collection
    {
        return collect(File::files($localePath))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->mapWithKeys(function ($file) {
                /** @var \SplFileInfo $file */
                $translations = $this->loadTranslationFile($file->getPathname());

                return [$file->getFilename() => $translations];
            });
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadTranslationFile(string $filePath): ?array
    {
        try {
            $translations = include $filePath;
        } catch (Throwable $exception) {
            $this->error("Impossible de charger {$filePath}: {$exception->getMessage()}");

            return null;
        }

        if (! is_array($translations)) {
            $this->error("Le fichier {$filePath} doit retourner un tableau.");

            return null;
        }

        return $translations;
    }

    /**
     * Crée un tableau de traductions vides avec la même structure que la référence.
     *
     * @param  array<string, mixed>  $referenceTranslations
     * @return array<string, mixed>
     */
    private function createEmptyTranslations(array $referenceTranslations): array
    {
        $result = [];

        foreach ($referenceTranslations as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->createEmptyTranslations($value);
            } else {
                $result[$key] = '';
            }
        }

        return $result;
    }

    /**
     * Écrit un fichier de traduction avec le format PHP approprié.
     *
     * @param  array<string, mixed>  $translations
     */
    private function writeTranslationFile(string $filePath, array $translations): bool
    {
        try {
            $content = "<?php\n\nreturn ".$this->arrayToPhpString($translations).";\n";
            File::put($filePath, $content);

            return true;
        } catch (Throwable $exception) {
            $this->error("Erreur lors de l'écriture de {$filePath}: {$exception->getMessage()}");

            return false;
        }
    }

    /**
     * Convertit un tableau en chaîne PHP formatée.
     *
     * @param  array<string, mixed>  $array
     */
    private function arrayToPhpString(array $array): string
    {
        return var_export($array, true);
    }

    /**
     * Demande à l'utilisateur de choisir une locale via interaction.
     */
    private function askForLocale(string $langPath, string $referenceLocale): ?string
    {
        $existingLocales = $this->getLanguageDirectories($langPath);
        $referencePath = $langPath.'/'.$referenceLocale;

        if (! File::isDirectory($referencePath)) {
            $this->error("La locale de référence '{$referenceLocale}' n'existe pas dans {$langPath}.");

            return null;
        }

        $this->info('Locales existantes: '.$existingLocales->keys()->implode(', '));
        $this->newLine();

        $suggestedLocales = [
            'es' => 'Espagnol',
            'de' => 'Allemand',
            'it' => 'Italien',
            'pt' => 'Portugais',
            'nl' => 'Néerlandais',
            'pl' => 'Polonais',
            'ru' => 'Russe',
            'ja' => 'Japonais',
            'zh' => 'Chinois',
            'ar' => 'Arabe',
        ];

        $availableSuggestions = collect($suggestedLocales)
            ->keys()
            ->reject(fn ($locale) => $existingLocales->has($locale))
            ->values()
            ->all();

        if (empty($availableSuggestions)) {
            $this->warn('Toutes les locales suggérées existent déjà.');
            $this->line('Veuillez entrer manuellement le code de la locale à ajouter (ex: es, de, it).');
            $locale = $this->ask('Locale à ajouter');

            if (empty($locale)) {
                $this->error('Locale non spécifiée.');

                return null;
            }

            return $locale;
        }

        $options = [];
        foreach ($availableSuggestions as $locale) {
            $name = $suggestedLocales[$locale] ?? $locale;
            $options[] = "{$locale} ({$name})";
        }

        $options[] = 'Autre (saisie manuelle)';

        $choice = $this->choice(
            'Sélectionnez la locale à ajouter',
            $options
        );

        if (str_contains($choice, 'Autre')) {
            $locale = $this->ask('Entrez le code de la locale à ajouter (ex: es, de, it)');

            if (empty($locale)) {
                $this->error('Locale non spécifiée.');

                return null;
            }

            return $locale;
        }

        return explode(' ', $choice)[0];
    }

    /**
     * Récupère les répertoires de langues disponibles.
     *
     * @return \Illuminate\Support\Collection<string, string>
     */
    private function getLanguageDirectories(string $langPath): \Illuminate\Support\Collection
    {
        return collect(File::directories($langPath))
            ->mapWithKeys(fn (string $dir) => [basename($dir) => str_replace('\\', '/', $dir)])
            ->sortKeys();
    }
}
