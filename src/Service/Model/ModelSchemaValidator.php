<?php

declare(strict_types=1);

namespace App\Rolling\Service\Model;

final class ModelSchemaValidator
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
        foreach (($schema['relations'] ?? []) as $nameEntity => $def) {
            if (!preg_match('/^[a-z][a-z0-9_]*$/', (string) $nameEntity)) {
                $errors[] = "Invalid relation name: $nameEntity";
            }
            if (!is_array($def) || !isset($def['of'])) {
                $errors[] = "Relation '$nameEntity' missing 'of'";
            }
        }

        return $errors;
    }
}
