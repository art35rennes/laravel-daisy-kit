<?php

namespace Art35rennes\DaisyKit\Helpers;

class TabErrorBag
{
    /**
     * Compte les erreurs par onglet en associant les champs aux IDs d'onglets.
     *
     * @param  array<string, array<string>>  $fieldToTabMap  Mapping des noms de champs vers les IDs d'onglets
     * @param  \Illuminate\Contracts\Support\MessageBag  $errors  Le bag d'erreurs de validation
     * @return array<string, int> Mapping des IDs d'onglets vers le nombre d'erreurs
     */
    public static function countErrorsByTab(array $fieldToTabMap, $errors): array
    {
        $counts = [];

        foreach ($fieldToTabMap as $field => $tabIds) {
            if (! is_array($tabIds)) {
                $tabIds = [$tabIds];
            }

            $fieldErrors = $errors->get($field);

            if (! empty($fieldErrors)) {
                foreach ($tabIds as $tabId) {
                    $counts[$tabId] = ($counts[$tabId] ?? 0) + count($fieldErrors);
                }
            }
        }

        return $counts;
    }

    /**
     * Crée un mapping automatique basé sur les préfixes de noms de champs.
     *
     * Exemple: si un champ s'appelle "general_name", il sera associé à l'onglet "general".
     *
     * @param  array<string>  $tabIds  Liste des IDs d'onglets
     * @param  \Illuminate\Contracts\Support\MessageBag  $errors  Le bag d'erreurs de validation
     * @return array<string, int> Mapping des IDs d'onglets vers le nombre d'erreurs
     */
    public static function countErrorsByTabPrefix(array $tabIds, $errors): array
    {
        $fieldToTabMap = [];

        foreach ($errors->keys() as $field) {
            foreach ($tabIds as $tabId) {
                if (str_starts_with($field, $tabId.'_')) {
                    $fieldToTabMap[$field][] = $tabId;
                }
            }
        }

        return self::countErrorsByTab($fieldToTabMap, $errors);
    }
}
