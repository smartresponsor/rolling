<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Policy;

interface VoterInterface
{
    public const int GRANT = 1;
    public const int DENY = -1;
    public const int ABSTAIN = 0;

    /**
     * @param array  $subject  arbitrary subject structure (id, roles, attributes)
     * @param string $action   Canonical action (e.g., "can_read").
     * @param array  $resource resource descriptor (type/id/attributes)
     * @param array  $context  optional context (tenant, request info)
     *
     * @return int one of GRANT, DENY, ABSTAIN
     */
    public function vote(array $subject, string $action, array $resource, array $context = []): int;

    /**
     * @return string
     */
    public function id(): string;
}
