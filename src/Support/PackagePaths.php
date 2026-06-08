<?php

namespace Art35rennes\DaisyKit\Support;

class PackagePaths
{
    public static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function config(): string
    {
        return self::path('config', 'daisy-kit.php');
    }

    public static function viewsComponents(): string
    {
        return self::path('resources', 'views', 'components');
    }

    public static function viewsTemplates(): string
    {
        return self::path('resources', 'views', 'templates');
    }

    public static function lang(): string
    {
        return self::path('resources', 'lang');
    }

    public static function js(): string
    {
        return self::path('resources', 'js');
    }

    public static function css(): string
    {
        return self::path('resources', 'css');
    }

    public static function distributableAssets(): string
    {
        return self::path('dist', 'vendor', 'art35rennes', 'laravel-daisy-kit');
    }

    public static function path(string ...$segments): string
    {
        return self::root().DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $segments);
    }
}
