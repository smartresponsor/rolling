<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Administration;

use App\Rolling\Service\Administration\RollingAdministrationPermissionCatalog;
use App\Rolling\Value\Administration\RollingFieldAccessDecision;
use App\Rolling\Value\Administration\RollingFieldAccessDecisionRequest;
use App\Rolling\Value\Administration\RollingFieldAccessScopeSet;
use App\Rolling\Value\Administration\RollingManagingFieldPermissionVocabulary;
use PHPUnit\Framework\TestCase;

final class RollingManagingFieldPermissionVocabularyTest extends TestCase
{
    public function testManagingFieldPermissionsAreExposedThroughAdministrationCatalog(): void
    {
        $catalog = new RollingAdministrationPermissionCatalog();
        $keys = $catalog->permissions();

        self::assertContains(RollingManagingFieldPermissionVocabulary::FIELD_VIEW, $keys);
        self::assertContains(RollingManagingFieldPermissionVocabulary::PROFILE_SELF_UPDATE, $keys);
        self::assertContains(RollingManagingFieldPermissionVocabulary::PROFILE_ROLE_UPDATE, $keys);
    }

    public function testFieldDecisionRequestUsesAttributesInsteadOfEasyAdminTypes(): void
    {
        $request = new RollingFieldAccessDecisionRequest(
            permissionKey: RollingManagingFieldPermissionVocabulary::FIELD_VIEW,
            componentKey: 'Managing',
            resourceClass: 'App\\Cataloging\\Entity\\Catalog\\CatalogCategoryEntity',
            fieldName: 'internalCost',
            pageName: 'detail',
            subjectIdentifier: 'user:42',
        );

        self::assertSame('managing.field.view', $request->toAttributes()['permission']);
        self::assertSame('internalCost', $request->toAttributes()['field']);
        self::assertSame('detail', $request->toAttributes()['page']);
    }

    public function testFieldAccessScopeSetBuildsSpecificToGlobalScopeChain(): void
    {
        $request = new RollingFieldAccessDecisionRequest(
            permissionKey: RollingManagingFieldPermissionVocabulary::FIELD_VIEW,
            componentKey: 'Managing',
            resourceClass: 'App\\Cataloging\\Entity\\Catalog\\CatalogCategoryEntity',
            fieldName: 'internalCost',
            pageName: 'detail',
            subjectIdentifier: 'user:42',
        );

        $scopes = RollingFieldAccessScopeSet::fromRequest($request)->scopes();

        self::assertSame(
            'component:managing:resource:app.cataloging.entity.catalog.catalogcategoryentity:page:detail:field:internalcost',
            $scopes[0],
        );
        self::assertSame('component:managing', $scopes[3]);
        self::assertSame('global', $scopes[4]);
    }

    public function testFieldViewDescriptorIncludesPageAndFieldScopeDimensions(): void
    {
        $descriptor = null;
        foreach (RollingManagingFieldPermissionVocabulary::descriptors() as $candidate) {
            if (RollingManagingFieldPermissionVocabulary::FIELD_VIEW === $candidate->key()) {
                $descriptor = $candidate;
                break;
            }
        }

        self::assertNotNull($descriptor);
        self::assertContains('page', $descriptor->scopes());
        self::assertContains('field', $descriptor->scopes());
        self::assertTrue($descriptor->sensitive());
    }

    public function testDecisionEffectHelpersAreExplicit(): void
    {
        self::assertTrue(RollingFieldAccessDecision::allow()->allowed());
        self::assertTrue(RollingFieldAccessDecision::deny()->denied());
        self::assertFalse(RollingFieldAccessDecision::abstain()->allowed());
    }
}
