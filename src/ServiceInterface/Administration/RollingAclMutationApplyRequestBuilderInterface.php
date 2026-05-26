<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationReview;

/**
 * Builds safe apply requests from reviewed ACL mutation dry-runs.
 */
interface RollingAclMutationApplyRequestBuilderInterface
{
    public function fromReview(
        string $requestKey,
        RollingAclMutationReview $review,
        string $requestedBySubject,
    ): RollingAclMutationApplyRequest;
}
