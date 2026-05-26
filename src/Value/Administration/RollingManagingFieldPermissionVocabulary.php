<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Rolling-owned catalog vocabulary for Managing field-level access and profile administration.
 *
 * These keys are capability-level permissions. Concrete resource, field, page, and subject details belong in
 * decision attributes so Rolling does not depend on EasyAdmin internals or generated CRUD controllers.
 */
final readonly class RollingManagingFieldPermissionVocabulary
{
    public const FIELD_VIEW = 'managing.field.view';
    public const FIELD_CONFIGURE = 'managing.field.configure';
    public const PROFILE_SELF_UPDATE = 'managing.field.profile.self_update';
    public const PROFILE_USER_UPDATE = 'managing.field.profile.user_update';
    public const PROFILE_ROLE_UPDATE = 'managing.field.profile.role_update';
    public const PROFILE_GROUP_UPDATE = 'managing.field.profile.group_update';
    public const PROFILE_ASSIGN = 'managing.field.profile.assign';

    /** @return list<RollingAdministrationPermissionDescriptor> */
    public static function descriptors(): array
    {
        return [
            new RollingAdministrationPermissionDescriptor(self::FIELD_VIEW, 'View Managing field values', 'managing_field_access', ['component', 'resource', 'page', 'field'], true),
            new RollingAdministrationPermissionDescriptor(self::FIELD_CONFIGURE, 'Configure Managing field access policy', 'managing_field_access', ['component', 'resource', 'page', 'field'], true),
            new RollingAdministrationPermissionDescriptor(self::PROFILE_SELF_UPDATE, 'Update own Managing field visibility profile', 'managing_field_profiles', ['component', 'resource', 'field']),
            new RollingAdministrationPermissionDescriptor(self::PROFILE_USER_UPDATE, 'Update a user Managing field visibility profile', 'managing_field_profiles', ['component', 'resource', 'user'], true),
            new RollingAdministrationPermissionDescriptor(self::PROFILE_ROLE_UPDATE, 'Update a role Managing field visibility profile', 'managing_field_profiles', ['component', 'resource', 'role'], true),
            new RollingAdministrationPermissionDescriptor(self::PROFILE_GROUP_UPDATE, 'Update a group Managing field visibility profile', 'managing_field_profiles', ['component', 'resource', 'group'], true),
            new RollingAdministrationPermissionDescriptor(self::PROFILE_ASSIGN, 'Assign Managing field visibility profiles', 'managing_field_profiles', ['component', 'resource', 'user', 'role', 'group'], true),
        ];
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_map(
            static fn (RollingAdministrationPermissionDescriptor $descriptor): string => $descriptor->key(),
            self::descriptors(),
        );
    }
}
