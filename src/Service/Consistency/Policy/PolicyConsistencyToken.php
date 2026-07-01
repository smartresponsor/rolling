<?php

declare(strict_types=1);

namespace App\Rolling\Service\Consistency\Policy;

final class PolicyConsistencyToken
{
    public function __construct(public int $rev)
    {
    }

    public function __toString(): string
    {
        return (string) $this->rev;
    }
}
