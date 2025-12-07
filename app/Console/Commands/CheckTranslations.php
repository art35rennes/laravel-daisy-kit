<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Throwable;

class CheckTranslations extends Command
{
    protected $signature = 'translations:check
        {--path= : Chemin personnalisé des traductions (par défaut resources/lang)}
        {--locale= : Locale de référence (par défaut app.fallback_locale)}';

    protected $description = 'Vérifie que chaque langue possède toutes les clés de traduction nécessaires.';

    public function handle(): int
    {
        $langPath = $this->resolveLangPath($this->option('path'));

        if (! File::isDirectory($langPath)) {
            $this->error("Dossier de traductions introuvable: {$langPath}");

            return Command::FAILURE;
        }

        $languages = $this->getLanguageDirectories($langPath);

        if ($languages->isEmpty()) {
            $this->warn("Aucune langue détectée dans {$langPath}.");

            return Command::SUCCESS;
        }

        $referenceLocale = $this->resolveReferenceLocale($languages->keys()->all(), $this->option('locale'));
        $referencePath = $languages[$referenceLocale];
        $referenceFiles = $this->getTranslationFiles($referencePath);

        if ($referenceFiles->isEmpty()) {
            $this->error("Aucun fichier de traduction trouvé pour la locale de référence {$referenceLocale}.");

            return Command::FAILURE;
        }

        $this->info("Locale de référence: {$referenceLocale}");
        $this->line('Locales disponibles (incluant référence): '.$languages->keys()->implode(', '));

        $referenceStats = $this->buildReferenceStats($referenceFiles);
        $this->line('Fichiers de référence: '.$referenceStats['files']);
        $this->line('Total clés de référence: '.$referenceStats['totalKeys']);
        $this->info('Dossier analysé: '.$langPath);

        $hasErrors = false;
        $missingCount = 0;
        $missingDetails = [];

        foreach ($languages as $locale => $path) {
            if ($locale === $referenceLocale) {
                continue;
            }

            foreach ($referenceFiles as $fileName => $referenceKeys) {
                if ($referenceKeys === null) {
                    $hasErrors = true;
                    $this->error("Fichier de référence invalide: {$fileName}");

                    continue;
                }

                $targetFile = $path.'/'.$fileName;

                if (! File::exists($targetFile)) {
                    $hasErrors = true;
                    $missingCount += count($referenceKeys);
                    $missingDetails[] = [
                        'locale' => $locale,
                        'file' => $fileName,
                        'keys' => $referenceKeys,
                    ];
                    $this->error("[{$locale}] Fichier manquant: {$fileName}");

                    continue;
                }

                $targetKeys = $this->extractKeys($targetFile);

                if ($targetKeys === null) {
                    $hasErrors = true;
                    $this->error("[{$locale}] Fichier invalide: {$fileName}");

                    continue;
                }

                $missingKeys = array_values(array_diff($referenceKeys, $targetKeys));

                if (! empty($missingKeys)) {
                    $hasErrors = true;
                    $missingCount += count($missingKeys);
                    $missingDetails[] = [
                        'locale' => $locale,
                        'file' => $fileName,
                        'keys' => $missingKeys,
                    ];
                    $this->displayMissingKeys($locale, $fileName, $missingKeys);
                }
            }
        }

        if ($hasErrors) {
            $this->newLine();
            $this->line("Clés manquantes totales: {$missingCount}");
            if (! empty($missingDetails)) {
                $this->line('Détail des clés manquantes (locale / fichier / clé):');
                foreach ($missingDetails as $detail) {
                    foreach ($detail['keys'] as $key) {
                        $this->line(" - [{$detail['locale']}] {$detail['file']} : {$key}");
                    }
                }
            }
            $this->error('Traductions incomplètes. Veuillez compléter les clés manquantes.');

            return Command::FAILURE;
        }

        $this->line('Clés manquantes totales: 0');
        $this->info('✓ Toutes les traductions sont complètes.');

        return Command::SUCCESS;
    }

    /**
     * @param Collection<string, array<int, string>|null> $referenceFiles
     * @return array{files:int,totalKeys:int}
     */
    private function buildReferenceStats(Collection $referenceFiles): array
    {
        $totalKeys = $referenceFiles
            ->filter()
            ->map(fn ($keys) => count($keys ?? []))
            ->sum();

        $fileCount = $referenceFiles->filter()->count();

        return [
            'files' => $fileCount,
            'totalKeys' => $totalKeys,
        ];
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
     * @param array<int, string> $locales
     */
    private function resolveReferenceLocale(array $locales, ?string $option): string
    {
        $preferred = $option ?: config('app.fallback_locale', 'en');

        if (in_array($preferred, $locales, true)) {
            return $preferred;
        }

        $fallback = $locales[0];
        $this->warn("Locale de référence {$preferred} introuvable, utilisation de {$fallback}.");

        return $fallback;
    }

    private function getLanguageDirectories(string $langPath): Collection
    {
        return collect(File::directories($langPath))
            ->mapWithKeys(fn (string $dir) => [basename($dir) => str_replace('\\', '/', $dir)])
            ->sortKeys();
    }

    /**
     * @return Collection<string, array<int, string>|null>
     */
    private function getTranslationFiles(string $localePath): Collection
    {
        return collect(File::files($localePath))
            ->filter(fn ($file) => $file->getExtension() === 'php')
            ->mapWithKeys(function ($file) {
                /** @var \SplFileInfo $file */
                $keys = $this->extractKeys($file->getPathname());

                return [$file->getFilename() => $keys];
            });
    }

    /**
     * @return array<int, string>|null
     */
    private function extractKeys(string $filePath): ?array
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

        return array_keys(Arr::dot($translations));
    }

    /**
     * @param array<int, string> $missingKeys
     */
    private function displayMissingKeys(string $locale, string $fileName, array $missingKeys): void
    {
        $this->error("[{$locale}] {$fileName}: ".count($missingKeys).' clé(s) manquante(s)');

        foreach (array_slice($missingKeys, 0, 10) as $key) {
            $this->line('  - '.$key);
        }

        if (count($missingKeys) > 10) {
            $this->line('  ...');
        }
    }
}

