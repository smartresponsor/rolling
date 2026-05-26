<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationPlannerInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationReviewBuilderInterface;
use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationReview;

/**
 * Builds safe operator review objects for Administering before ACL mutation
 * execution is allowed.
 */
final class RollingAclMutationReviewBuilder implements RollingAclMutationReviewBuilderInterface
{
    public function __construct(private readonly RollingAclMutationPlannerInterface $planner)
    {
    }

    public function review(RollingAclMutationRequest $request): RollingAclMutationReview
    {
        return RollingAclMutationReview::fromPlan($this->planner->plan($request));
    }
}
