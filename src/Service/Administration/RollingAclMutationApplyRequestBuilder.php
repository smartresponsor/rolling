<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationApplyRequestBuilderInterface;
use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationReview;

/**
 * Default metadata-only builder for ACL mutation apply requests.
 */
final readonly class RollingAclMutationApplyRequestBuilder implements RollingAclMutationApplyRequestBuilderInterface
{
    public function fromReview(
        string $requestKey,
        RollingAclMutationReview $review,
        string $requestedBySubject,
    ): RollingAclMutationApplyRequest {
        return RollingAclMutationApplyRequest::fromReview(
            $requestKey,
            $review,
            $requestedBySubject,
            [
                'builder' => self::class,
                'apply_mode' => $review->valid() ? 'ready_for_acl_service' : 'blocked_by_invalid_review',
            ],
        );
    }
}
