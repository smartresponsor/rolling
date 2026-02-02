<?php
declare(strict_types=1);

namespace Model;

/**
 *
 */

/**
 *
 */
final class Validation
{
    /** @return list<string> */
    public static function validate(array $schema): array
    {
        $errors = [];
        if (!isset($schema['namespace']) || !is_string($schema['namespace'])) {
            $errors[] = "Missing 'namespace' (string)";
        }
        if (!isset($schema['relations']) || !is_array($schema['relations'])) {
            $errors[] = "Missing 'relations' (map)";
        }
        // simple keys/values check
        foreach (($schema['relations'] ?? []) as $name => $def) {
            if (!preg_match('/^[a-z][a-z0-9_]*$/', (string)$name)) {
                $errors[] = "Invalid relation name: $name";
            }
            if (!is_array($def) || !isset($def['of'])) {
                $errors[] = "Relation '$name' missing 'of'";
            }
        }
        return $errors;
    }
}
