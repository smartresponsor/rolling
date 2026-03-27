<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Pdp\Dto;

/**
 * Immutable DTO describing a permission decision input.
 */
final class DecisionRequest
{
    /**
     * @param array $subject @param array<string,mixed> $resource @param array<string,mixed> $context
     * @param string $action
     * @param array $resource
     * @param array $context
     */
    public function __construct(
        public readonly array  $subject,
        public readonly string $action,
        public readonly array  $resource,
        public readonly array  $context = [],
    ) {}
}
