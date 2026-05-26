<?php

declare(strict_types=1);

namespace Tests\Role\Administration;

use App\Rolling\Service\Administration\RollingAclMutationPlanner;
use App\Rolling\Service\Administration\RollingAclMutationValidator;
use App\Rolling\Value\Administration\RollingAclMutationRequest;
use PHPUnit\Framework\TestCase;

final class RollingAclMutationPlannerSafeContextTest extends TestCase
{
    public function testPlannerPreservesAdministeringSafeContextForFieldAccessReviews(): void
    {
        $planner = new RollingAclMutationPlanner(new RollingAclMutationValidator());

        $plan = $planner->plan(new RollingAclMutationRequest(
            mutationType: 'acl.allow',
            subjectIdentifier: 'user:42',
            permissionOrRoleKey: 'managing.field.view',
            scopeKey: 'component:managing:resource:app.cataloging.entity.product:page:detail:field:internalcost',
            requestedBySubject: 'administering:admin',
            safeContext: [
                'source' => 'administering_ui',
                'surface' => 'managing_field_access_mutation_review',
                'target' => ['component' => 'Managing', 'field' => 'internalCost'],
            ],
        ));

        self::assertSame('managing_field_access_mutation_review', $plan->safeContext()['surface']);
        self::assertSame('Managing', $plan->safeContext()['target']['component']);
        self::assertSame('administering:admin', $plan->safeContext()['requested_by_subject']);
    }
}
