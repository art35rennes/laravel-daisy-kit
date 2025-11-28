<?php

namespace Art35rennes\DaisyKit\Helpers;

use Illuminate\Support\Facades\Session;

class WizardPersistence
{
    /**
     * Récupère toutes les données du wizard depuis la session.
     */
    public static function get(string $key = 'wizard'): array
    {
        return Session::get("{$key}.data", []);
    }

    /**
     * Récupère une valeur spécifique depuis les données du wizard.
     */
    public static function getValue(string $field, mixed $default = null, string $key = 'wizard'): mixed
    {
        $data = self::get($key);

        return $data[$field] ?? $default;
    }

    /**
     * Enregistre des données dans la session du wizard.
     */
    public static function put(array $data, string $key = 'wizard'): void
    {
        $existing = self::get($key);
        Session::put("{$key}.data", array_merge($existing, $data));
    }

    /**
     * Enregistre une valeur spécifique dans les données du wizard.
     */
    public static function putValue(string $field, mixed $value, string $key = 'wizard'): void
    {
        $data = self::get($key);
        $data[$field] = $value;
        Session::put("{$key}.data", $data);
    }

    /**
     * Supprime toutes les données du wizard de la session.
     */
    public static function forget(string $key = 'wizard'): void
    {
        Session::forget("{$key}.data");
        Session::forget("{$key}.current_step");
    }

    /**
     * Récupère l'étape courante du wizard.
     */
    public static function getCurrentStep(string $key = 'wizard'): ?int
    {
        return Session::get("{$key}.current_step");
    }

    /**
     * Enregistre l'étape courante du wizard.
     */
    public static function setCurrentStep(int $step, string $key = 'wizard'): void
    {
        Session::put("{$key}.current_step", $step);
    }
}
