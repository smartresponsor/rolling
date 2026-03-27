<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Pipeline;

/**
 *
 */

/**
 *
 */
final class Decision
{
    /**
     * @param bool $allow
     * @param string $reason
     * @param array $headers
     * @param array $explain
     */
    public function __construct(public bool $allow, public string $reason, public array $headers = [], public array $explain = []) {}

    /**
     * @param \Pipeline\Trace $t
     * @param string $reason
     * @param array $headers
     * @return self
     */
    public static function allowed(Trace $t, string $reason = 'ok', array $headers = []): self
    {
        return new self(true, $reason, $headers, $t->all());
    }

    /**
     * @param \Pipeline\Trace $t
     * @param string $reason
     * @param array $headers
     * @return self
     */
    public static function denied(Trace $t, string $reason = 'deny', array $headers = []): self
    {
        return new self(false, $reason, $headers, $t->all());
    }
}
