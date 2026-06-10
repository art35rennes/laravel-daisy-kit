<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

it('does not ship executable inline scripts or event handler attributes in Blade views', function () {
    $views = collect(File::allFiles(dirname(__DIR__, 2).'/resources/views'))
        ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php');

    $violations = [];

    foreach ($views as $view) {
        $contents = $view->getContents();
        $relativePath = $view->getRelativePathname();

        if (preg_match_all('/<script\b([^>]*)>/i', $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes = $match[1] ?? '';
                $isJsonPayload = preg_match('/\btype\s*=\s*["\']application\/json["\']/i', $attributes) === 1;
                $isJsonLdPayload = preg_match('/\btype\s*=\s*["\']application\/ld\+json["\']/i', $attributes) === 1;
                $hasNonce = str_contains($attributes, 'nonce=')
                    || str_contains($attributes, 'PackageAsset::nonceAttribute()');

                if ($isJsonLdPayload && ! $hasNonce) {
                    $violations[] = "{$relativePath} contains JSON-LD script without CSP nonce support";
                }

                if (! $isJsonPayload && ! $isJsonLdPayload) {
                    $violations[] = "{$relativePath} contains executable inline script";
                }
            }
        }

        if (preg_match('/\son[a-zA-Z]+\s*=/i', $contents)) {
            $violations[] = "{$relativePath} contains inline event handler attribute";
        }

        if (preg_match('/\sx-(?:on|data|init|effect)\b/i', $contents)) {
            $violations[] = "{$relativePath} contains inline Alpine expression attribute";
        }

        if (preg_match('/\s@(?:click|change|input|submit|keydown|keyup|load|error)(?:[.\w-]+)?\s*=/i', $contents)) {
            $violations[] = "{$relativePath} contains shorthand Alpine event expression";
        }
    }

    expect($violations)->toBe([]);
});

it('renders a strict csp host smoke page without inline executable code or inline styles', function () {
    config([
        'daisy-kit.csp_nonce' => 'smoke-nonce',
        'daisy-kit.use_vite' => false,
        'daisy-kit.auto_assets' => false,
        'daisy-kit.bundle.css' => 'vendor/daisy-kit/daisy-kit.css',
        'daisy-kit.bundle.js' => 'vendor/daisy-kit/daisy-kit.js',
    ]);

    $html = Blade::render(<<<'BLADE'
        <!doctype html>
        <html lang="en">
            <head>
                <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'nonce-smoke-nonce'; style-src 'self' 'nonce-smoke-nonce'">
                @include('daisy::components.partials.assets')
                @stack('styles')
            </head>
            <body>
                <x-daisy::ui.layout.hero image-url="/img/example.jpg">
                    <x-daisy::ui.data-display.radial-progress :value="92" size="7rem" thickness="0.7rem" color="primary" />
                    <x-daisy::ui.inputs.range :no-fill="true" />
                    <x-daisy::ui.media.embed src="/frame" />
                </x-daisy::ui.layout.hero>

                <x-daisy::ui.navigation.breadcrumbs :json-ld="true" :items="[
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Current'],
                ]" />

                @stack('scripts')
            </body>
        </html>
    BLADE);

    $violations = renderedHtmlCspViolations($html);

    expect($violations)->toBe([]);
});

function renderedHtmlCspViolations(string $html): array
{
    $violations = [];

    if (preg_match('/\sstyle\s*=/i', $html)) {
        $violations[] = 'rendered HTML contains an inline style attribute';
    }

    if (preg_match('/\son[a-zA-Z]+\s*=/i', $html)) {
        $violations[] = 'rendered HTML contains an inline event handler attribute';
    }

    if (preg_match('/\sx-(?:on|data|init|effect)\b/i', $html)) {
        $violations[] = 'rendered HTML contains an inline Alpine expression attribute';
    }

    if (preg_match('/\s@(?:click|change|input|submit|keydown|keyup|load|error)(?:[.\w-]+)?\s*=/i', $html)) {
        $violations[] = 'rendered HTML contains a shorthand Alpine event expression';
    }

    if (preg_match_all('/<script\b([^>]*)>/i', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attributes = $match[1] ?? '';
            $isJsonPayload = preg_match('/\btype\s*=\s*["\']application\/json["\']/i', $attributes) === 1;
            $isJsonLdPayload = preg_match('/\btype\s*=\s*["\']application\/ld\+json["\']/i', $attributes) === 1;
            $hasNonce = preg_match('/\bnonce\s*=\s*["\'][^"\']+["\']/i', $attributes) === 1;
            $hasSrc = preg_match('/\bsrc\s*=/i', $attributes) === 1;

            if ($isJsonLdPayload && ! $hasNonce) {
                $violations[] = 'rendered JSON-LD script is missing a CSP nonce';
            }

            if (! $isJsonPayload && ! $isJsonLdPayload && ! ($hasSrc && $hasNonce)) {
                $violations[] = 'rendered HTML contains executable inline script or script without nonce';
            }
        }
    }

    if (preg_match_all('/<style\b([^>]*)>/i', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $attributes = $match[1] ?? '';
            $hasNonce = preg_match('/\bnonce\s*=\s*["\'][^"\']+["\']/i', $attributes) === 1;

            if (! $hasNonce) {
                $violations[] = 'rendered HTML contains style tag without CSP nonce';
            }
        }
    }

    return $violations;
}

it('does not ship inline style attributes in Blade views', function () {
    $views = collect(File::allFiles(dirname(__DIR__, 2).'/resources/views'))
        ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'php');

    $violations = [];

    foreach ($views as $view) {
        $contents = $view->getContents();
        $relativePath = $view->getRelativePathname();

        if (preg_match('/\sstyle\s*=/i', $contents)) {
            $violations[] = "{$relativePath} contains an inline style attribute";
        }

        if (preg_match_all('/<style\b([^>]*)>/i', $contents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attributes = $match[1] ?? '';
                $hasNonce = str_contains($attributes, 'nonce=')
                    || str_contains($attributes, 'PackageAsset::nonceAttribute()');

                if (! $hasNonce) {
                    $violations[] = "{$relativePath} contains a style tag without CSP nonce support";
                }
            }
        }
    }

    expect($violations)->toBe([]);
});

it('does not use dynamic javascript evaluation in package modules', function () {
    $scripts = collect(File::allFiles(dirname(__DIR__, 2).'/resources/js'))
        ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'js');

    $violations = [];

    foreach ($scripts as $script) {
        $contents = $script->getContents();
        $relativePath = $script->getRelativePathname();

        if (preg_match('/\beval\s*\(/', $contents)) {
            $violations[] = "{$relativePath} uses eval()";
        }

        if (preg_match('/\bnew\s+Function\b/', $contents)) {
            $violations[] = "{$relativePath} uses new Function";
        }

        if (preg_match('/document\.createElement\(\s*[\'"]style[\'"]\s*\)/', $contents)) {
            $violations[] = "{$relativePath} injects a style tag at runtime";
        }

        if (preg_match('/\.on[a-z]+\s*=/', $contents)) {
            $violations[] = "{$relativePath} assigns inline event handler properties";
        }
    }

    expect($violations)->toBe([]);
});

it('does not rely on runtime value variables for daisyui value components', function () {
    $views = collect([
        dirname(__DIR__, 2).'/resources/views/components/ui/data-display/radial-progress.blade.php',
        dirname(__DIR__, 2).'/resources/views/components/ui/advanced/countdown.blade.php',
    ]);

    $violations = [];

    foreach ($views as $path) {
        $contents = File::get($path);
        $relativePath = str_replace(dirname(__DIR__, 2).'/', '', $path);

        if (str_contains($contents, 'data-daisy-css-value')) {
            $violations[] = "{$relativePath} uses data-daisy-css-value instead of static CSP-safe value classes";
        }
    }

    expect($violations)->toBe([]);
});

it('does not emit package runtime css variable shims in public resources', function () {
    $files = collect([
        ...File::allFiles(dirname(__DIR__, 2).'/resources/views'),
        ...File::allFiles(dirname(__DIR__, 2).'/resources/js'),
    ])->filter(fn (SplFileInfo $file) => in_array($file->getExtension(), ['php', 'js'], true));

    $violations = [];

    foreach ($files as $file) {
        $contents = $file->getContents();
        $relativePath = str_replace(dirname(__DIR__, 2).'/', '', $file->getPathname());

        if (preg_match('/data-daisy-css-[a-z-]+/', $contents)) {
            $violations[] = "{$relativePath} depends on data-daisy-css runtime style shims";
        }
    }

    expect($violations)->toBe([]);
});

it('keeps public module runtime style writes explicitly audited', function () {
    $violations = [];

    $scripts = collect(File::allFiles(dirname(__DIR__, 2).'/resources/js'))
        ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'js');

    foreach ($scripts as $script) {
        $contents = $script->getContents();
        $relativePath = $script->getRelativePathname();

        if (preg_match('/\.style(?:\.|\.setProperty\s*\()/i', $contents)) {
            $violations[] = "{$relativePath} writes runtime inline styles";
        }
    }

    expect($violations)->toBe([]);
});

it('does not ship dynamic javascript evaluation in built browser assets', function () {
    $assetsPath = dirname(__DIR__, 2).'/dist/vendor/art35rennes/laravel-daisy-kit/assets';

    if (! is_dir($assetsPath)) {
        $this->markTestSkipped('Built package assets are not present.');
    }

    $scripts = collect(File::allFiles($assetsPath))
        ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'js');

    $violations = [];

    foreach ($scripts as $script) {
        $contents = $script->getContents();
        $relativePath = $script->getRelativePathname();

        if (preg_match('/\beval\s*\(/', $contents)) {
            $violations[] = "{$relativePath} uses eval()";
        }

        if (preg_match('/\bnew\s+Function\b/', $contents)) {
            $violations[] = "{$relativePath} uses new Function";
        }

        if (preg_match('/\bnew\s+Ajv\b|\bajv\.compile\b/i', $contents)) {
            $violations[] = "{$relativePath} ships runtime AJV compilation";
        }
    }

    expect($violations)->toBe([]);
});
