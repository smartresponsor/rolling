<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationServiceInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationAuditRecorderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\Value\Administration\RollingAclMutationAuditEvent;
use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;

/**
 * Safe bootstrap implementation until Doctrine-backed ACL mutation storage is wired.
 */
final class BootstrapRollingAclAdministrationService implements RollingAclAdministrationServiceInterface
{
    public function __construct(
        private readonly RollingAclMutationAuditRecorderInterface $auditRecorder,
        private readonly RollingAclMutationValidatorInterface $mutationValidator,
    ) {
    }

    public function mutate(RollingAclMutationRequest $request): RollingAclMutationResult
    {
        $validation = $this->mutationValidator->validate($request);
        if (!$validation->valid()) {
            $result = RollingAclMutationResult::rejected(
                'Rolling ACL mutation request failed validation.',
                [
                    'mutation_type' => $request->mutationType(),
                    'violations' => $validation->violations(),
                ],
            );

            $this->auditRecorder->record(RollingAclMutationAuditEvent::fromResult($request, $result));

            return $result;
        }

        $result = RollingAclMutationResult::rejected(
            'Rolling ACL mutation storage is not wired yet.',
            [
                'mutation_type' => $request->mutationType(),
                'subject_identifier' => $request->subjectIdentifier(),
                'scope_key' => $request->scopeKey(),
            ],
        );

        $this->auditRecorder->record(RollingAclMutationAuditEvent::fromResult($request, $result));

        return $result;
    }
}
