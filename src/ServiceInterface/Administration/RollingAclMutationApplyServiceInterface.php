<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;
use App\Rolling\Value\Administration\RollingAclMutationReview;

/**
 * Applies reviewed ACL mutations through Rolling-owned services.
 */
interface RollingAclMutationApplyServiceInterface
{
    public function apply(RollingAclMutationApplyRequest $request): RollingAclMutationResult;

    public function applyReviewedMutation(
        string $requestKey,
        RollingAclMutationReview $review,
        string $requestedBySubject,
    ): RollingAclMutationResult;
}
