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
final class DecisionResult
{
    /**
     * @param bool $allow
     * @param string $policyVersion
     * @param string|null $ruleId
     * @param array $obligations
     * @param array $meta
     */
    public function __construct(
        public bool    $allow,
        public string  $policyVersion,
        public ?string $ruleId = null,
        public array   $obligations = [],
        public array   $meta = [],
    ) {}
}
