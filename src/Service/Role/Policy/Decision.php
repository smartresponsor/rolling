<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Policy;

/**
 *
 */

/**
 *
 */
final class Decision
{
    /**
     * @param bool $allowed
     * @param array $meta
     */
    public function __construct(
        public bool  $allowed,
        /** @var array<string, mixed> */
        public array $meta = [],
    ) {}

    /**
     * @param array $meta
     * @return self
     */
    public static function allow(array $meta = []): self
    {
        return new self(true, $meta);
    }

    /**
     * @param array $meta
     * @return self
     */
    public static function deny(array $meta = []): self
    {
        return new self(false, $meta);
    }
}
