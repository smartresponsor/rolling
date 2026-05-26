<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationServiceInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionEventRecorderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionGatewayInterface;
use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationExecutionEvent;
use App\Rolling\Value\Administration\RollingAclMutationResult;

/**
 * Thin execution gateway for reviewed ACL apply requests.
 *
 * The gateway deliberately delegates to RollingAclAdministrationServiceInterface
 * so Administering never becomes the ACL mutation owner.
 */
final readonly class RollingAclMutationExecutionGateway implements RollingAclMutationExecutionGatewayInterface
{
    public function __construct(
        private RollingAclAdministrationServiceInterface $aclAdministrationService,
        private RollingAclMutationExecutionEventRecorderInterface $executionEventRecorder,
    ) {
    }

    public function execute(RollingAclMutationApplyRequest $request): RollingAclMutationResult
    {
        if (!$request->reviewValid()) {
            $result = RollingAclMutationResult::rejected(
                'Reviewed ACL mutation is invalid and cannot be executed.',
                [
                    'request_key' => $request->requestKey(),
                    'reason' => 'invalid_review',
                ],
            );
            $this->executionEventRecorder->record(RollingAclMutationExecutionEvent::fromApplyRequest($request, $result));

            return $result;
        }

        $result = $this->aclAdministrationService->mutate($request->toMutationRequest());
        $this->executionEventRecorder->record(RollingAclMutationExecutionEvent::fromApplyRequest($request, $result));

        return $result;
    }
}
