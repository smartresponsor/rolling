<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\ServiceInterface\Role\Policy;

/**
 *
 */

/**
 *
 */
interface VoterInterface
{
    public const GRANT = 1;
    public const DENY = -1;
    public const ABSTAIN = 0;

    /**
     * @param array $subject Arbitrary subject structure (id, roles, attributes).
     * @param string $action Canonical action (e.g., "can_read").
     * @param array $resource Resource descriptor (type/id/attributes).
     * @param array $context Optional context (tenant, request info).
     * @return int One of GRANT, DENY, ABSTAIN.
     */
    public function vote(array $subject, string $action, array $resource, array $context = []): int;

    /**
     * @return string
     */
    public function id(): string;
}
