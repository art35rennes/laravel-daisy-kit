<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use RuntimeException;

abstract class AbstractScanner
{
    /**
     * @param  array<int, string>  $paths
     */
    protected static function computeFilesHash(array $paths): string
    {
        $mtimes = [];

        foreach ($paths as $path) {
            $mtime = @filemtime($path);
            $mtimes[$path] = is_int($mtime) ? $mtime : 0;
        }

        ksort($mtimes);

        return md5(json_encode($mtimes, JSON_THROW_ON_ERROR));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected static function writePhpCacheFile(string $cachePath, array $payload): void
    {
        File::ensureDirectoryExists(dirname($cachePath));

        $tmpPath = $cachePath.'.tmp';
        $content = "<?php\n\nreturn ".var_export($payload, true).";\n";

        File::put($tmpPath, $content);

        if (File::exists($cachePath)) {
            File::delete($cachePath);
        }

        File::move($tmpPath, $cachePath);
    }

    /**
     * @return array<string, mixed>
     */
    protected static function readPhpCacheFile(string $cachePath): array
    {
        if (! File::exists($cachePath)) {
            throw new RuntimeException("Cache file not found: {$cachePath}");
        }

        $data = require $cachePath;

        if (! is_array($data)) {
            throw new RuntimeException("Invalid cache file: {$cachePath}");
        }

        return $data;
    }
}
