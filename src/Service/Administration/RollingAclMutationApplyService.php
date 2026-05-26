<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationApplyRequestBuilderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationApplyServiceInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionGatewayInterface;
use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;
use App\Rolling\Value\Administration\RollingAclMutationReview;

/**
 * Rolling-owned apply orchestration for reviewed ACL mutations.
 */
final readonly class RollingAclMutationApplyService implements RollingAclMutationApplyServiceInterface
{
    public function __construct(
        private RollingAclMutationApplyRequestBuilderInterface $applyRequestBuilder,
        private RollingAclMutationExecutionGatewayInterface $executionGateway,
    ) {
    }

    public function apply(RollingAclMutationApplyRequest $request): RollingAclMutationResult
    {
        return $this->executionGateway->execute($request);
    }

    public function applyReviewedMutation(
        string $requestKey,
        RollingAclMutationReview $review,
        string $requestedBySubject,
    ): RollingAclMutationResult {
        $applyRequest = $this->applyRequestBuilder->fromReview($requestKey, $review, $requestedBySubject);

        return $this->executionGateway->execute($applyRequest);
    }
}
