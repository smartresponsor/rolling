<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Administration;

use App\Rolling\Value\Administration\RollingManagingFieldAccessScopeVocabulary;
use App\Rolling\Value\Administration\RollingManagingFieldPermissionVocabulary;
use PHPUnit\Framework\TestCase;

final class RollingManagingFieldAccessScopeVocabularyTest extends TestCase
{
    public function testFieldAccessPermissionIsSeparatedFromProfileAndConfigurePermissions(): void
    {
        self::assertTrue(RollingManagingFieldAccessScopeVocabulary::isFieldAccessPermission(
            RollingManagingFieldPermissionVocabulary::FIELD_VIEW,
        ));
        self::assertFalse(RollingManagingFieldAccessScopeVocabulary::isFieldAccessPermission(
            RollingManagingFieldPermissionVocabulary::FIELD_CONFIGURE,
        ));
        self::assertFalse(RollingManagingFieldAccessScopeVocabulary::isFieldAccessPermission(
            RollingManagingFieldPermissionVocabulary::PROFILE_SELF_UPDATE,
        ));
    }

    public function testAllowedMutationTypesMatchAdministeringFieldAccessCorridor(): void
    {
        self::assertSame([
            'permission.grant',
            'permission.revoke',
            'acl.allow',
            'acl.deny',
        ], RollingManagingFieldAccessScopeVocabulary::allowedMutationTypes());
    }

    public function testRequiredDecisionAttributesDocumentRuntimeContract(): void
    {
        self::assertSame([
            'permission',
            'component',
            'resource',
            'field',
            'page',
            'operation',
            'subject',
        ], RollingManagingFieldAccessScopeVocabulary::requiredDecisionAttributes());
    }
}
