<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Legacy\ServiceInterface\Policy\Obligation;

/**
 *
 */

/**
 *
 */
interface ObligationApplierInterface
{
    /**
     * Apply post-decision obligations (e.g., masking/redaction) to returned resource payload.
     *
     * @param array $subject
     * @param string $action
     * @param array $resource Original resource payload
     * @param array $context
     * @return array{resource: array, meta: array}
     */
    public function apply(array $subject, string $action, array $resource, array $context = []): array;
}
