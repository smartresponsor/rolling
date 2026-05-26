<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Canonical scope and mutation vocabulary for Managing field-value access.
 *
 * Rolling owns the authorization semantics, but this vocabulary keeps field access separate from profile
 * presentation preferences and from Administering control-plane permissions.
 */
final readonly class RollingManagingFieldAccessScopeVocabulary
{
    public const COMPONENT_KEY = 'managing';
    public const FIELD_ACCESS_PERMISSION = RollingManagingFieldPermissionVocabulary::FIELD_VIEW;

    public const SCOPE_FIELD = 'field';
    public const SCOPE_PAGE = 'page';
    public const SCOPE_RESOURCE = 'resource';
    public const SCOPE_COMPONENT = 'component';
    public const SCOPE_GLOBAL = 'global';

    /** @return non-empty-list<string> */
    public static function scopeLevels(): array
    {
        return [
            self::SCOPE_FIELD,
            self::SCOPE_PAGE,
            self::SCOPE_RESOURCE,
            self::SCOPE_COMPONENT,
            self::SCOPE_GLOBAL,
        ];
    }

    /** @return non-empty-list<string> */
    public static function allowedMutationTypes(): array
    {
        return [
            'permission.grant',
            'permission.revoke',
            'acl.allow',
            'acl.deny',
        ];
    }

    /** @return non-empty-list<string> */
    public static function requiredDecisionAttributes(): array
    {
        return [
            'permission',
            'component',
            'resource',
            'field',
            'page',
            'operation',
            'subject',
        ];
    }

    public static function componentScopePrefix(): string
    {
        return 'component:'.self::COMPONENT_KEY;
    }

    public static function isFieldAccessPermission(string $permissionKey): bool
    {
        return self::FIELD_ACCESS_PERMISSION === trim($permissionKey);
    }

    public static function isAllowedMutationType(string $mutationType): bool
    {
        return in_array(trim($mutationType), self::allowedMutationTypes(), true);
    }
}
