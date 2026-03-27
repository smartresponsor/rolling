<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Policy\Voter;

use App\ServiceInterface\Role\Policy\VoterInterface;

/**
 * Attribute-based decisions: owner writes own resource, etc.
 * Expects resource['ownerId'] and subject['id'] for owner rule.
 */
final class AttributeVoter implements VoterInterface
{
    /**
     * @return string
     */
    public function id(): string
    {
        return 'attr';
    }

    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @return int
     */
    public function vote(array $subject, string $action, array $resource, array $context = []): int
    {
        if (($resource['ownerId'] ?? null) && ($subject['id'] ?? null)) {
            if ($resource['ownerId'] === $subject['id']) {
                // owner can_read/can_write by default; tighten per product later.
                if ($action === 'can_read' || $action === 'can_write') {
                    return self::GRANT;
                }
            }
        }
        return self::ABSTAIN;
    }
}
