<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Support;

use JsonException;

class JsonConfiguration
{
    /** @param array<string, mixed> $configuration */
    public static function encode(array $configuration): string
    {
        return json_encode(
            $configuration,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR,
        );
    }

    /** @return array<string, mixed>|null */
    public static function decode(string $json): ?array
    {
        try {
            $configuration = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        if (! is_array($configuration)) {
            return null;
        }

        $result = [];

        foreach ($configuration as $key => $value) {
            if (! is_string($key)) {
                return null;
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
