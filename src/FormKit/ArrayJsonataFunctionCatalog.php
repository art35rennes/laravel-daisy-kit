<?php

namespace Art35rennes\DaisyKit\FormKit;

use Art35rennes\DaisyKit\FormKit\Contracts\JsonataFunctionCatalog;

/**
 * Config-driven {@see JsonataFunctionCatalog} backed by `daisy-kit.forms.jsonata.function_catalog`.
 */
class ArrayJsonataFunctionCatalog extends JsonataFunctionCatalog
{
    /**
     * @param  array<int, array<string, mixed>>  $functions  Raw catalog rows from configuration or tests.
     */
    public function __construct(
        protected array $functions = [],
    ) {}

    /**
     * @return static
     */
    public static function fromConfig(): self
    {
        $functions = config('daisy-kit.forms.jsonata.function_catalog', []);

        return new self(is_array($functions) ? $functions : []);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function functions(): array
    {
        return array_values($this->functions);
    }
}
