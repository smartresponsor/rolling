<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Context;

/**
 *
 */

/**
 *
 */
final class EnvContext
{
    /** @return array<string,mixed> */
    public function capture(): array
    {
        $sub = getenv('ROLE_SUBJECT') ?: '';
        return $sub ? ['subject' => $sub] : [];
    }
}
