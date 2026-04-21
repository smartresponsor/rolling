<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Policy\Voter;

use App\Rolling\ServiceInterface\Policy\VoterInterface;

/**
 * Enforce tenant isolation: subject.tenant must equal context.tenant/resource.tenant if given.
 * On mismatch -> DENY, otherwise ABSTAIN (does not grant).
 */
final class TenantBoundaryVoter implements VoterInterface
{
    /**
     * @return string
     */
    public function id(): string
    {
        return 'tenant-boundary';
    }

    /**
     * @param array  $subject
     * @param string $action
     * @param array  $resource
     * @param array  $context
     *
     * @return int
     */
    public function vote(array $subject, string $action, array $resource, array $context = []): int
    {
        $subTenant = $subject['tenant'] ?? null;
        $ctxTenant = $context['tenant'] ?? ($resource['tenant'] ?? null);
        if (null !== $subTenant && null !== $ctxTenant && $subTenant !== $ctxTenant) {
            return self::DENY;
        }

        return self::ABSTAIN;
    }
}
