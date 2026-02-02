<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace Resilience;

use Throwable;

/**
 *
 */

/**
 *
 */
final class ErrorClassifier
{
    /**
     * Return true if error is *permanent* (do not retry).
     */
    public static function isPermanent(Throwable $e): bool
    {
        $code = $e->getCode();
        // Example mapping: 4xx → permanent; 5xx → transient
        if ($code >= 400 && $code < 500) return true;
        return false;
    }
}
