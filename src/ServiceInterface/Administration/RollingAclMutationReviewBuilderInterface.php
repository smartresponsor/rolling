<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationReview;

interface RollingAclMutationReviewBuilderInterface
{
    public function review(RollingAclMutationRequest $request): RollingAclMutationReview;
}
