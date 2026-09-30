<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\DTO\Pdp;

/**
 * Immutable DTO describing a permission decision input.
 */
final class PdpDecisionRequestDTO
{
    public function __construct(
        public readonly array $subject,
        public readonly string $action,
        public readonly array $resource,
        public readonly array $context = [],
    ) {
    }
}
