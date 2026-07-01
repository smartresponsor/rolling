<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Cache;

/**
 * Versioned tag invalidation interface.
 */
interface TagInvalidatorInterface
{
    /** @param string[] $tags */
    public function invalidateTags(array $tags): void;

    public function bumpTag(string $tag): void;

    public function getTagVersion(string $tag): int;
}
