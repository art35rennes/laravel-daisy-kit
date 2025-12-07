<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class ComponentPropsParser
{
    /**
     * Parse un fichier Blade et extrait les informations des props.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function parse(string $filePath): array
    {
        if (! File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $props = [];

        // Extraire le bloc @props([...])
        // Utiliser une regex plus permissive pour gérer les retours à la ligne
        if (! preg_match('/@props\s*\(\s*\[(.*?)\]\s*\)/s', $content, $matches)) {
            return [];
        }

        $propsBlock = $matches[1];
        
        // Nettoyer le bloc (retirer les espaces en début/fin)
        $propsBlock = trim($propsBlock);

        // Parser chaque ligne de prop
        $lines = explode("\n", $propsBlock);
        $currentComment = '';

        foreach ($lines as $line) {
            $originalLine = $line;
            $line = trim($line);

            // Ignorer les lignes vides
            if (empty($line)) {
                continue;
            }

            // Détecter un commentaire multiligne avant une prop
            if (preg_match('/^\/\/\s*(.+)$/', $line, $commentMatch)) {
                $currentComment = trim($commentMatch[1]);
                continue;
            }

            // Parser une prop : 'name' => 'default', // comment | values
            // Supporte aussi: 'name' => 'default', et 'name' => 'default'
            // Regex améliorée pour gérer les cas avec et sans virgule finale
            $matched = false;
            if (preg_match("/^'([^']+)'\s*=>\s*(.+?)(?:,\s*)?(?:\/\/\s*(.+))?$/", $line, $propMatch)) {
                $matched = true;
            } elseif (preg_match("/^'([^']+)'\s*=>\s*(.+?)(?:\/\/\s*(.+))?$/", $line, $propMatch)) {
                $matched = true;
            }
            
            if ($matched) {
                $name = $propMatch[1];
                $defaultValueStr = trim($propMatch[2], " \t,");
                $defaultValue = self::parseValue($defaultValueStr);
                $inlineComment = isset($propMatch[3]) ? trim($propMatch[3]) : '';

                // Extraire les valeurs possibles depuis le commentaire inline
                $values = self::extractValues($inlineComment);

                // Extraire la description
                $description = self::extractDescription($inlineComment, $currentComment);

                // Déduire le type
                $type = self::inferType($defaultValue);

                $props[] = [
                    'name' => $name,
                    'type' => $type,
                    'default' => $defaultValue,
                    'values' => $values,
                    'description' => $description,
                ];

                // Réinitialiser le commentaire après utilisation
                $currentComment = '';
            }
        }

        return $props;
    }

    /**
     * Parse une valeur PHP (string, bool, null, etc.).
     */
    private static function parseValue(string $value): mixed
    {
        $value = trim($value, " \t\n\r\0\x0B,");

        // null
        if ($value === 'null') {
            return null;
        }

        // bool
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        // string (entre quotes)
        if (preg_match("/^['\"](.*)['\"]$/", $value, $match)) {
            return $match[1];
        }

        // number
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        // array (simple)
        if (preg_match('/^\[(.*)\]$/', $value, $match)) {
            return [];
        }

        // Retourner tel quel si on ne peut pas parser
        return $value;
    }

    /**
     * Extrait les valeurs possibles depuis un commentaire (format: value1 | value2 | value3).
     */
    private static function extractValues(string $comment): ?array
    {
        if (empty($comment)) {
            return null;
        }

        // Chercher le pattern: value1 | value2 | value3 ou value1|value2|value3
        // Peut être au début, au milieu ou à la fin du commentaire
        // Exemples: "xs | sm | md | lg | xl", "xs|sm|md", "primary | secondary | accent"
        if (preg_match('/([a-z0-9_\-\s\|]+(?:\s*\|\s*[a-z0-9_\-\s]+)+)/i', $comment, $match)) {
            $valuesStr = trim($match[1]);
            // Séparer par pipe (avec ou sans espaces)
            $values = preg_split('/\s*\|\s*/', $valuesStr);
            // Filtrer les valeurs vides et nettoyer
            $values = array_map('trim', $values);
            $values = array_filter($values, fn ($v) => ! empty($v) && strlen($v) > 0);

            return ! empty($values) ? array_values($values) : null;
        }

        return null;
    }

    /**
     * Extrait la description depuis les commentaires.
     */
    private static function extractDescription(string $inlineComment, string $multilineComment): string
    {
        // Priorité au commentaire multiligne
        if (! empty($multilineComment)) {
            $desc = $multilineComment;
            // Retirer les valeurs possibles si présentes (format: description: value1 | value2)
            $desc = preg_replace('/:\s*[a-z0-9_\-\s\|]+$/i', '', $desc);
            $desc = preg_replace('/\s*[a-z0-9_\-\s\|]+\s*$/i', '', $desc);

            return trim($desc);
        }

        // Sinon utiliser le commentaire inline (sans les valeurs)
        if (! empty($inlineComment)) {
            $desc = $inlineComment;
            // Retirer les valeurs possibles (peuvent être au début ou à la fin)
            // Format: "value1 | value2" ou "description: value1 | value2"
            $desc = preg_replace('/^[a-z0-9_\-\s\|]+\s*\|\s*/i', '', $desc);
            $desc = preg_replace('/:\s*[a-z0-9_\-\s\|]+$/i', '', $desc);
            $desc = preg_replace('/\s*[a-z0-9_\-\s\|]+\s*$/i', '', $desc);

            return trim($desc);
        }

        return '';
    }

    /**
     * Déduit le type PHP depuis la valeur par défaut.
     */
    private static function inferType(mixed $value): string
    {
        if (is_null($value)) {
            return 'mixed';
        }

        if (is_bool($value)) {
            return 'bool';
        }

        if (is_int($value)) {
            return 'int';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_array($value)) {
            return 'array';
        }

        if (is_string($value)) {
            return 'string';
        }

        return 'mixed';
    }

    /**
     * Parse les props d'un composant depuis son chemin de fichier.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function parseComponent(string $category, string $name): array
    {
        $filePath = resource_path("views/components/ui/{$category}/{$name}.blade.php");

        return self::parse($filePath);
    }
}

