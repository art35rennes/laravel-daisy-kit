<?php

namespace Art35rennes\DaisyKit\FormKit\Contracts;

/**
 * Supplies JSONata function metadata (name, signature, documentation) for tooling and JS catalog registration.
 */
abstract class JsonataFunctionCatalog
{
    /**
     * @return array<int, array<string, mixed>>
     */
    abstract public function functions(): array;
}
