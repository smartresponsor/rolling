<?php

declare(strict_types=1);

namespace App\Rolling\Service\Consistency\Rebac;

final class RebacConsistencyToken
{
    public function __construct(public int $rev)
    {
    }

    public function __toString(): string
    {
        return (string) $this->rev;
    }
}
