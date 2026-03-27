<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Audit\Dto;

/**
 *
 */

/**
 *
 */
final class DecisionInput
{
    /**
     * @param array $subject
     * @param string $action
     * @param array $resource
     * @param array $context
     * @param array $voterTrace
     */
    public function __construct(
        public array  $subject,
        public string $action,
        public array  $resource,
        public array  $context = [],
        /** @var array<int, array<string,mixed>> $voterTrace */
        public array  $voterTrace = [],
    ) {}
}
