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

        $conditionNames = [];
        if (isset($schema['conditions'])) {
            if (!is_array($schema['conditions'])) {
                $errors[] = "Invalid 'conditions' (map)";
            } else {
                foreach ($schema['conditions'] as $conditionName => $conditionDef) {
                    if (!self::isName((string) $conditionName)) {
                        $errors[] = "Invalid condition name: $conditionName";
                    }
                    if (!is_array($conditionDef) || !isset($conditionDef['expression']) || !is_string($conditionDef['expression']) || '' === trim($conditionDef['expression'])) {
                        $errors[] = "Condition '$conditionName' missing non-empty 'expression'";
                    }
                    $conditionNames[(string) $conditionName] = true;
                }
            }
        }

        foreach (($schema['relations'] ?? []) as $nameEntity => $def) {
            if (!self::isName((string) $nameEntity)) {
                $errors[] = "Invalid relation name: $nameEntity";
            }
            if (!is_array($def) || !isset($def['of'])) {
                $errors[] = "Relation '$nameEntity' missing 'of'";
                continue;
            }

            foreach (self::stringList($def['of']) as $subjectType) {
                if (!self::isSubjectType($subjectType)) {
                    $errors[] = "Relation '$nameEntity' has invalid subject type: $subjectType";
                }
            }

            if (isset($def['condition'])) {
                if (!is_string($def['condition']) || !self::isName($def['condition'])) {
                    $errors[] = "Relation '$nameEntity' has invalid condition reference";
                } elseif (!isset($conditionNames[$def['condition']])) {
                    $errors[] = "Relation '$nameEntity' references unknown condition: {$def['condition']}";
                }
            }
        }

        if (isset($schema['permissions'])) {
            if (!is_array($schema['permissions'])) {
                $errors[] = "Invalid 'permissions' (map)";
            } else {
                foreach ($schema['permissions'] as $permissionName => $permissionDef) {
                    if (!self::isName((string) $permissionName)) {
                        $errors[] = "Invalid permission name: $permissionName";
                    }
                    if (!is_array($permissionDef) || !isset($permissionDef['via'])) {
                        $errors[] = "Permission '$permissionName' missing 'via'";
                        continue;
                    }
                    foreach (self::stringList($permissionDef['via']) as $relationName) {
                        if (!self::isName($relationName)) {
                            $errors[] = "Permission '$permissionName' has invalid relation reference: $relationName";
                        } elseif (!isset($schema['relations'][$relationName])) {
                            $errors[] = "Permission '$permissionName' references unknown relation: $relationName";
                        }
                    }
                }
            }
        }

        return $errors;
    }

    private static function isName(string $name): bool
    {
        return 1 === preg_match('/^[a-z][a-z0-9_]*$/', $name);
    }

    private static function isSubjectType(string $subjectType): bool
    {
        return 1 === preg_match('/^[a-z][a-z0-9_]*(#[a-z][a-z0-9_]*)?$/', $subjectType);
    }

    /** @return list<string> */
    private static function stringList(mixed $value): array
    {
        if (is_string($value)) {
            return [$value];
        }

        if (!is_array($value)) {
            return [''];
        }

        return array_values(array_map(static fn (mixed $item): string => is_string($item) ? $item : '', $value));
    }
}
