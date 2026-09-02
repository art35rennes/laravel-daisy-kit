<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Tree;

use DOMDocument;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;

class TreeConfiguration
{
    /** @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public static function make(array $options): array
    {
        foreach (['valueMode' => ['leaves', 'selected-roots'], 'searchMode' => ['auto', 'manual'], 'searchMatch' => ['includes', 'fuzzy']] as $key => $allowed) {
            if (! in_array($options[$key], $allowed, true)) {
                throw new InvalidArgumentException("Invalid tree {$key}.");
            }
        }
        foreach (['searchDebounce', 'searchMin'] as $key) {
            if (! is_int($options[$key]) || $options[$key] < 0) {
                throw new InvalidArgumentException("Tree {$key} must be a non-negative integer.");
            }
        }
        foreach (['multiple', 'disabled', 'searchable', 'hasInitialValue', 'highlightMatches'] as $key) {
            if (! is_bool($options[$key])) {
                throw new InvalidArgumentException("Tree {$key} must be boolean.");
            }
        }
        foreach (['name', 'persistenceKey', 'nodeView'] as $key) {
            if ($options[$key] !== null && (! is_string($options[$key]) || trim($options[$key]) === '')) {
                throw new InvalidArgumentException("Invalid tree {$key}.");
            }
        }

        if (! is_string($options['searchParam']) || ! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $options['searchParam'])) {
            throw new InvalidArgumentException('Invalid tree search parameter.');
        }
        self::validateSource($options['searchSource']);
        $ids = [];
        $templates = [];

        if ($options['nodeView'] !== null && ! View::exists($options['nodeView'])) {
            throw new InvalidArgumentException('Unknown tree node view.');
        }
        $options['items'] = self::nodes($options['items'], $ids, $templates, $options['nodeView']);
        $value = $options['value'];

        if ($options['multiple']) {
            if ($value !== null && (! is_array($value) || ! array_is_list($value))) {
                throw new InvalidArgumentException('Multiple tree value must be an array.');
            }
            $options['value'] = array_values(array_unique(array_map(self::id(...), $value ?? [])));
        } elseif ($value !== null) {
            $options['value'] = self::id($value);
        }

        if (! is_array($options['initialExpandPaths']) || ! array_is_list($options['initialExpandPaths'])) {
            throw new InvalidArgumentException('Tree expansion paths must be an array.');
        }
        $options['initialExpandPaths'] = array_map(function (mixed $path): array {
            if (! is_array($path) || ! array_is_list($path) || $path === []) {
                throw new InvalidArgumentException('Every expansion path must be a non-empty array.');
            }

            return array_map(self::id(...), $path);
        }, $options['initialExpandPaths']);

        if (! is_array($options['labels'])) {
            throw new InvalidArgumentException('Tree labels must be an array.');
        }
        foreach ($options['labels'] as $message) {
            if (! is_string($message)) {
                throw new InvalidArgumentException('Tree labels must be strings.');
            }
        }
        $labels = __('daisy-kit::tree');
        $options['labels'] = array_replace(is_array($labels) ? $labels : [], $options['labels']);
        unset($options['nodeView']);

        return ['configuration' => $options, 'templates' => $templates];
    }

    private static function id(mixed $id): string
    {
        if ((! is_string($id) && ! is_int($id)) || (string) $id === '') {
            throw new InvalidArgumentException('Tree ids must be non-empty strings or integers.');
        }

        return (string) $id;
    }

    private static function validateSource(mixed $source): void
    {
        if ($source === null) {
            return;
        }

        if (! is_string($source) || trim($source) === '' || preg_match('/[\x00-\x20\\\\]/', $source)) {
            throw new InvalidArgumentException('Invalid tree source URL.');
        }
        $parts = parse_url($source);

        if ($parts === false || isset($parts['user']) || isset($parts['pass'])
            || (isset($parts['scheme']) && ! in_array(strtolower($parts['scheme']), ['http', 'https'], true))) {
            throw new InvalidArgumentException('Tree sources must use HTTP or HTTPS.');
        }
    }

    /** @param array<string, bool> $ids
     * @param  array<string, string>  $templates
     * @return list<array<string, mixed>>
     */
    private static function nodes(mixed $items, array &$ids, array &$templates, ?string $view, int $depth = 0): array
    {
        if (! is_array($items) || ! array_is_list($items) || $depth > 64) {
            throw new InvalidArgumentException('Tree items must be a list, at most 64 levels deep.');
        }
        $result = [];
        foreach ($items as $item) {
            if (! is_array($item) || ! is_string($item['label'] ?? null) || trim($item['label']) === '') {
                throw new InvalidArgumentException('Every tree item requires an id and label.');
            }
            $id = self::id($item['id'] ?? null);

            if (isset($ids[$id])) {
                throw new InvalidArgumentException("Duplicate tree id: {$id}.");
            }
            $ids[$id] = true;
            self::validateSource($item['source'] ?? null);
            foreach (['disabled', 'expanded'] as $key) {
                if (isset($item[$key]) && ! is_bool($item[$key])) {
                    throw new InvalidArgumentException("Tree {$key} must be boolean.");
                }
            }
            foreach (['description', 'badge'] as $key) {
                if (isset($item[$key]) && ! is_string($item[$key])) {
                    throw new InvalidArgumentException("Tree {$key} must be text.");
                }
            }

            if ($view !== null) {
                $html = View::make($view, ['node' => $item])->render();
                self::validatePresentation($html);
                $templates[$id] = $html;
            }
            $normalized = array_intersect_key($item, array_flip(['label', 'description', 'badge', 'source', 'disabled', 'expanded']));
            $normalized['id'] = $id;
            $normalized['children'] = self::nodes($item['children'] ?? [], $ids, $templates, $view, $depth + 1);
            $result[] = $normalized;
        }

        return $result;
    }

    private static function validatePresentation(string $html): void
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<div>'.$html.'</div>', LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            foreach ($document->getElementsByTagName('*') as $element) {
                if (in_array(strtolower($element->tagName), ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'select', 'textarea', 'a', 'link', 'meta', 'base'], true)) {
                    throw new InvalidArgumentException('Tree node views must contain inert presentation markup only.');
                }
                foreach ($element->attributes as $attribute) {
                    $name = strtolower($attribute->name);

                    if (str_starts_with($name, 'on') || in_array($name, ['style', 'tabindex', 'contenteditable', 'srcdoc', 'autofocus'], true)) {
                        throw new InvalidArgumentException('Tree node views cannot contain executable or focusable attributes.');
                    }

                    if (in_array($name, ['src', 'href', 'xlink:href'], true)) {
                        self::validateSource($attribute->value);
                    }
                }
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
