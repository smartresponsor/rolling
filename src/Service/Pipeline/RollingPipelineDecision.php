<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Pipeline;

final class RollingPipelineDecision
{
    public function __construct(public bool $allow, public string $reason, public array $headers = [], public array $explain = [])
    {
    }

    /**
     * @param array<string,mixed> $headers
     */
    public static function allowed(RollingPipelineTrace $t, string $reason = 'ok', array $headers = []): self
    {
        return new self(true, $reason, $headers, $t->all());
    }

    /**
     * @param array<string,mixed> $headers
     */
    public static function denied(RollingPipelineTrace $t, string $reason = 'deny', array $headers = []): self
    {
        return new self(false, $reason, $headers, $t->all());
    }
}
