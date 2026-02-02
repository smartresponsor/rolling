<?php
declare(strict_types=1);

namespace App\Consistency\Role\Rebac;
/**
 *
 */

/**
 *
 */
final class Token
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
        return (string)$this->rev;
    }
}
