<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationPlannerInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\Value\Administration\RollingAclMutationPlan;
use App\Rolling\Value\Administration\RollingAclMutationRequest;

/**
 * Builds a safe dry-run plan before ACL mutations are persisted.
 */
final class RollingAclMutationPlanner implements RollingAclMutationPlannerInterface
{
    public function __construct(private readonly RollingAclMutationValidatorInterface $validator)
    {
    }

    public function plan(RollingAclMutationRequest $request): RollingAclMutationPlan
    {
        $validation = $this->validator->validate($request);
        $steps = [
            'Validate mutation request.',
            'Resolve subject identifier within the application ACL namespace.',
            'Resolve permission or role key from Rolling catalog.',
            'Apply mutation through Doctrine-backed ACL storage when enabled.',
            'Record ACL mutation audit event.',
        ];

        $warnings = $validation->valid()
            ? ['Doctrine-backed ACL mutation storage is not wired in this bootstrap phase.']
            : $validation->violations();

        return new RollingAclMutationPlan(
            $request->mutationType(),
            $request->subjectIdentifier(),
            $request->permissionOrRoleKey(),
            $request->scopeKey(),
            $validation->valid(),
            $steps,
            $warnings,
            array_merge($request->safeContext(), [
                'requested_by_subject' => $request->requestedBySubject(),
                'storage_mode' => 'bootstrap-dry-run',
            ]),
        );
    }
}
