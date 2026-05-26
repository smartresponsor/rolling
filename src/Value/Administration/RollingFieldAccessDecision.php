<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Explicit Rolling decision for field-level access.
 */
final readonly class RollingFieldAccessDecision
{
    public const EFFECT_ALLOW = 'allow';
    public const EFFECT_DENY = 'deny';
    public const EFFECT_ABSTAIN = 'abstain';

    public function __construct(
        public string $effect,
        public string $source = 'rolling',
        public ?string $reason = null,
    ) {
    }

    public static function allow(?string $reason = null): self
    {
        return new self(self::EFFECT_ALLOW, reason: $reason);
    }

    public static function deny(?string $reason = null): self
    {
        return new self(self::EFFECT_DENY, reason: $reason);
    }

    public static function abstain(?string $reason = null): self
    {
        return new self(self::EFFECT_ABSTAIN, reason: $reason);
    }

    public function allowed(): bool
    {
        return self::EFFECT_ALLOW === $this->effect;
    }

    public function denied(): bool
    {
        return self::EFFECT_DENY === $this->effect;
    }
}
