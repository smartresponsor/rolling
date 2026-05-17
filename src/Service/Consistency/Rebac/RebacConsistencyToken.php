<?php

declare(strict_types=1);

namespace App\Rolling\Service\Consistency\Rebac;

final class RebacConsistencyToken
{
    /**
     * @param int $rev
     */
    public function __construct(public int $rev)
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->rev;
    }
}
