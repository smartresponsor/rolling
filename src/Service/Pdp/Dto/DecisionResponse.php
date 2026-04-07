<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Pdp\Dto;

/**
 * Result of a single decision, with explain metadata.
 */
final class DecisionResponse
{
    /**
     * @param bool $allowed
     * @param string $ruleId
     * @param string $reason
     * @param float $latencyMs
     */
    public function __construct(
        public readonly bool   $allowed,
        public readonly string $ruleId,
        public readonly string $reason,
        public readonly float  $latencyMs,
    )
    {
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'allowed' => $this->allowed,
            'ruleId' => $this->ruleId,
            'reason' => $this->reason,
            'latencyMs' => $this->latencyMs,
        ];
    }
}
