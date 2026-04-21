<?php

declare(strict_types=1);

namespace App\Rolling\Policy\V2;

use App\Rolling\Policy\Obligation\Obligations;

final class DecisionWithObligations
{
    public function __construct(
        private readonly bool $allow,
        private readonly string $reason,
        public Obligations $obligations,
    ) {
    }

    public static function allow(string $reason, ?Obligations $obligations = null): self
    {
        return new self(true, $reason, $obligations ?? Obligations::empty());
    }

    public static function deny(string $reason, ?Obligations $obligations = null): self
    {
        return new self(false, $reason, $obligations ?? Obligations::empty());
    }

    public function isAllow(): bool
    {
        return $this->allow;
    }

    public function isDeny(): bool
    {
        return !$this->allow;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function obligations(): Obligations
    {
        return $this->obligations;
    }
}
