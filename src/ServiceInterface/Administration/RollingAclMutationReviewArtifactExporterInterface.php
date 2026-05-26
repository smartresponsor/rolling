<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationReview;
use App\Rolling\Value\Administration\RollingAclMutationReviewArtifact;

interface RollingAclMutationReviewArtifactExporterInterface
{
    public function export(RollingAclMutationReview $review): RollingAclMutationReviewArtifact;
}
