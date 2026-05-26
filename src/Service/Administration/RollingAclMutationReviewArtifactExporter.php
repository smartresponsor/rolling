<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationReviewArtifactExporterInterface;
use App\Rolling\Value\Administration\RollingAclMutationReview;
use App\Rolling\Value\Administration\RollingAclMutationReviewArtifact;

final class RollingAclMutationReviewArtifactExporter implements RollingAclMutationReviewArtifactExporterInterface
{
    public function export(RollingAclMutationReview $review): RollingAclMutationReviewArtifact
    {
        $payload = [
            'type' => 'rolling_acl_mutation_review',
            'mutation_type' => $review->mutationType(),
            'subject_identifier' => $review->subjectIdentifier(),
            'permission_or_role_key' => $review->permissionOrRoleKey(),
            'scope_key' => $review->scopeKey(),
            'valid' => $review->valid(),
            'step_count' => count($review->steps()),
            'warning_count' => count($review->warnings()),
            'violation_count' => count($review->violations()),
            'review' => $review->toSafeArray(),
        ];

        $encoded = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        return new RollingAclMutationReviewArtifact(
            sprintf('rolling-acl-review-%s', hash('xxh128', $encoded)),
            hash('sha256', $encoded),
            $payload,
        );
    }
}
