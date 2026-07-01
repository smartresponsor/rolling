<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Policy;

final class PolicyEngineDecision
{
    public function __construct(
        public bool $allowed,
        /** @var array<string, mixed> */
        public array $meta = [],
    ) {
    }

    public static function allow(array $meta = []): self
    {
        return new self(true, $meta);
    }

    public static function deny(array $meta = []): self
    {
        return new self(false, $meta);
    }
}
